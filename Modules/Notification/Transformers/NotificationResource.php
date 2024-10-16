<?php

namespace Modules\Notification\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            ...$this->data,
            'notificationId' => $this->id,
            'createdAt' => $this->created_at,
            'readAt' => $this->read_at,
            'isRead' => $this->read_at ? true : false,
            'id' => $this->data['id'] ?? '-',
            'type' => $this->getNotificationType($this->type),
            'title' => $this->data['title'] ?? '-',
            'desc' => $this->truncateHTML($this->data['desc'] ?? '-', 150),
            'htmlContent' => $this->data['desc'] ?? '-',
            'image' => '',
            'time' => Carbon::parse($this->created_at)->diffForHumans()
        ];
    }

    private function getNotificationType($type)
    {
        $types = [
            'Modules\Notification\Notifications\Announcement' => 'announcement',
            'Modules\Notification\Notifications\Task' => 'task',
            'Modules\Notification\Notifications\Content' => 'content',
            'Modules\Notification\Notifications\CourseTask' => 'course-task',
            'Modules\Notification\Notifications\CourseContent' => 'course-content'
        ];
        return $types[$type] ?? '-';
    }

    private function truncateHTML($html, $limit)
    {
        $cleanedHtml = str_replace('&nbsp;', ' ', $html);
        $plainText = strip_tags($cleanedHtml);
        $truncatedText = mb_substr($plainText, 0, $limit);
        return $truncatedText . (mb_strlen($plainText) > $limit ? '...' : '');
    }
}
