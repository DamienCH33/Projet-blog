<?php

abstract class AbstractController
{
    /**
     * Vérifie que l'utilisateur est connecté.
     * @return void
     */
    protected function checkIfUserIsConnected(): void
    {
        // On vérifie que l'utilisateur est connecté.
        if (!isset($_SESSION['user'])) {
            Utils::redirect("connectionForm");
        }
    }
}