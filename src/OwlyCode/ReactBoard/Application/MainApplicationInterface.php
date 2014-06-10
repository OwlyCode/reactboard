<?php

namespace OwlyCode\ReactBoard\Application;

interface MainApplicationInterface extends ApplicationInterface
{
    public function getDefaultAppName();

    public function getDefaultModule();

    public function getCurrentModule();

    public function setCurrentModule($module);

    public function getCurrentApplication();

    public function setCurrentApplication(ApplicationInterface $application);

    public function setApplications(ApplicationRepository $applications);
}
