<?php


namespace App\Services;


class AnalyticsService
{
    public function getUsersCount(): int
    {
        return 3;
    }

    public function setUsersCount(int $param)
    {
        echo "[Analytics Service] Added User\n";
    }
}