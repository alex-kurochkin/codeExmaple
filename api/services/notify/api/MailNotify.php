<?php

declare(strict_types=1);

namespace api\services\notify\api;

use api\models\user\User;
use Yii;

abstract class MailNotify
{
    private array $to;
    private array $from;
    private string $subject;
    private string $html;
    private string $text;

    public function setTo(array $to): void
    {
        $this->to = $to;
    }

    public function setFrom(array $from): void
    {
        $this->from = $from;
    }

    public function renderSubject(User $user, string $serverId, string $emailTarget): void
    {
        $subjectFile = $this->makeTemplateFilename($serverId, $emailTarget . '-subject', $user->language);
        $this->subject = Yii::$app->controller->renderFile($subjectFile, ['user' => $user]);
    }

    protected function makeTemplateFilename(string $serverId, string $emailTarget, string $language): string
    {
        return Yii::getAlias('@common/mail/') . $serverId . '/' . $language . '/' . $emailTarget . '.php';
    }

    public function renderHtml(User $user, string $serverId, string $emailTarget): void
    {
        $htmlFile = $this->makeTemplateFilename($serverId, $emailTarget . '-html', $user->language);
        $this->html = Yii::$app->controller->renderFile($htmlFile, ['user' => $user]);
    }

    public function renderText(User $user, string $serverId, string $emailTarget): void
    {
        $textFile = $this->makeTemplateFilename($serverId, $emailTarget . '-text', $user->language);
        $this->text = Yii::$app->controller->renderFile($textFile, ['user' => $user]);
    }

    public function getMailData(): array
    {
        return [
            'to' => $this->to,
            'from' => $this->from,
            'subject' => $this->subject,
            'html' => $this->html,
            'text' => $this->text,
        ];
    }
}