<?php
class shopCustomPluginAjaxActions extends waJsonActions
{
    public function emailAction()
    {
        $phone = waRequest::post('phone');
        $errors = array();
        $send = false;
        if (!$phone) {
            $errors['phone'] = 'Пожалуйста введите телефон';
        } elseif (!preg_match("/^[\+0-9\-\(\)\s]{10,20}$/", $phone)) {
            $errors['phone'] = 'Введите корректный телефон';
        }

        if( !$errors ) {

            $general = wa('shop')->getConfig()->getGeneralSettings();
            $product = htmlspecialchars(waRequest::post('product'));
            $product_url = htmlspecialchars(waRequest::post('product_url'));
            $rand = rand(0, 1000);
            $subject = "Заказ в один клик с сайта FENIX-RUSSIA.RU".$rand;
            $to = "info@fonarik.com";
            $from = $general['email'];

            $body = 'Клиент не захотел заполнять поля при оформлении заказа и хочет быстро оформить заказ по телефону.<br><br>Товар, который хочет купить клиент: ' . $product . '<br><br>Ссылка на товар: <a href="' . $product_url . '">' . $product_url . '"</a><br><br>Телефон клиента для обратной связи: ' . $phone . '<br><br><div style="display:none;">'. $rand .'</div>';
            $message = new waMailMessage($subject, $body);
            $message->setTo($to);
            if ($from) {
                $message->setFrom($from, $general['name']);
            }
            $send = $message->send();
        }
        $this->response = array(
            'errors' => $errors,
            'send' => $send
        );
    }

    public function reviewEditAction() {
        $review_id = waRequest::post('id', null, waRequest::TYPE_INT);
        if (!$review_id) {
            throw new waException("Unknown review id");
        }
        $name = waRequest::post('name', null, waRequest::TYPE_STRING_TRIM);
        $text = waRequest::post('text', null, waRequest::TYPE_STRING_TRIM);
        if (!$name || !$text) {
            throw new waException("Review cant be update");
        }

        $product_reviews_model = new shopProductReviewsModel();

        $data = array(
            'name' => $name,
            'text' => $text,
        );
        $product_reviews_model->updateById($review_id, $data);
    }
}