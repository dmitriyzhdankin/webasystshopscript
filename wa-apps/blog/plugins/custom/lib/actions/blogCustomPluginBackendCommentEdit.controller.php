<?php
class blogCustomPluginBackendCommentEditController extends waJsonController
{
    public function execute()
    {
        $this->getResponse()->addHeader('Content-type', 'application/json');
        if ($comment_id = $this->getRequest()->post('id', 0, waRequest::TYPE_INT)) {
            $comment_model = new blogCommentModel();

            $comment = $comment_model->getById($comment_id);
            if (!$comment) {
                throw new waException(_w('Comment not found'), 404);
            }
            $post_model = new blogPostModel();
            if (!($post = $post_model->getBlogPost(array('id'=>$comment['post_id'], 'blog_id'=>$comment['blog_id'])))) {
                throw new waException(_w('Post not found'), 404);
            }


            $user_id = $this->getUser()->getId();
            $rights = blogHelper::checkRights($comment['blog_id'], $user_id, blogRightConfig::RIGHT_READ_WRITE);
            if ($rights == blogRightConfig::RIGHT_READ_WRITE && ($user_id != $post['contact_id'])) {
                throw new waRightsException(_w('Access denied'), 403);
            }


            $text = $this->getRequest()->post('text', null, waRequest::TYPE_STRING_TRIM);
            $name = $this->getRequest()->post('name', null, waRequest::TYPE_STRING_TRIM);

            $changed = $comment_model->updateById($comment_id, array('text'=>$text,'name'=>$name));
            $count = $comment_model->getCount($comment['blog_id'], $comment['post_id']);
//            if ($changed) {
//                if ($status == blogCommentModel::STATUS_DELETED) {
//                    $this->log('comment_delete', 1);
//                } else {
//                    $this->log('comment_restore', 1);
//                }
//            }
            $this->response = array(
                'count_str' => $count." "._w('comment', 'comments', $count),
                'text' =>$text,
                'name' =>$name,
                'changed'=>$changed,
            );
        }
    }
}