<?php
$tab = Utils::request("tab", "dashboard");

include_once '_navbar.php';

switch ($tab) {
    case "dashboard":
        require 'tab-dashboard.php';
        break;
    case "monitoring":
        require 'tab-monitoring.php';
        break;
    default:
        echo '<p>Onglet inconnu.</p>';
        break;
}