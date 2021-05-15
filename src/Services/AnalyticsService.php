<?php


namespace App\Services;


class AnalyticsService
{
    public function onUserCreated() {
        $userCount = $this->getUsersCount();
        $this->setUsersCount($userCount + 1);
    }

    public function getUsersCount(): int
    {
        return 3;
    }

    public function setUsersCount(int $param)
    {
        echo "[Analytics Service] Added User\n";
    }
}