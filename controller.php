<?php
namespace Concrete\Package\OunziwFormAutoreply;

use Concrete\Core\Package\Package;
use Concrete\Core\Support\Facade\Events;

class Controller extends Package {

    protected $pkgHandle = 'ounziw_form_autoreply';
    protected $appVersionRequired = '8.4';
    protected $pkgVersion = '0.7';

    public function getPackageDescription()
    {
        return t("Auto Reply for Legacy Form");
    }

    public function getPackageName()
    {
        return t("Auto Reply for Legacy Form");
    }


    public function on_start()
    {
        Events::addListener('on_form_submission', function($event) {
            $formEventData = $event->getArgument('formData');
            if (filter_var($formEventData['replyToEmailAddress'],FILTER_VALIDATE_EMAIL)) {
                $mh = $this->app->make('mail');
                $mh->setSubject('自動返信: フォーム送信ありがとうございました。');

                $to_email = $formEventData['replyToEmailAddress'];
                $from_email = 'info@calculator.jp';

                $mailbody = '自動返信: フォーム送信ありがとうございました。' . PHP_EOL;
                $mailbody .= PHP_EOL;
                $mailbody .= '下記の内容を受け取りました。' . PHP_EOL;
                $mailbody .= PHP_EOL;
                foreach($formEventData['questionAnswerPairs'] as $questionAnswerPair){
                    $mailbody .= $questionAnswerPair['question'] . PHP_EOL . $questionAnswerPair['answerDisplay'] . PHP_EOL . PHP_EOL;
                }
                $mailbody .= PHP_EOL;
                $mailbody .= '担当者より、後ほどお返事を差し上げます。今しばらくお待ちくださいませ。' . PHP_EOL;
                $mailbody .= PHP_EOL;
                $mailbody .= '--' . PHP_EOL;
                $mailbody .= '自動計算サイト' . PHP_EOL;
                $mailbody .= 'https://calculator.jp/' . PHP_EOL;
                $mailbody .= 'info@calculator.jp' . PHP_EOL;

                $mh->to($to_email);
                $mh->from($from_email);
                $mh->setBody($mailbody);
                $mh->sendMail();
            } else {
                \Log::addEntry("replyToEmailAddress is empty or invalid.");
            }
        });
    }
}