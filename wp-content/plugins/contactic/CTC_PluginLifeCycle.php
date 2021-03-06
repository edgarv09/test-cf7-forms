<?php
/*
    "Contact Form to Database" Copyright (C) 2011-2012 Michael Simpson  (email : michael.d.simpson@gmail.com)

    This file is part of Contactic.

    Contactic is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Contactic is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Contactic.
    If not, see <http://www.gnu.org/licenses/>.
*/

require_once('CTC_InstallIndicator.php');

/**
 * All the basic plugin life cycle functionality is implemented herein.
 * A Plugin is expected to subclass this class and override method to inject
 * its own specific behaviors.
 *
 * @author Michael Simpson
 */

abstract class CTC_PluginLifeCycle extends CTC_InstallIndicator {

    public function install() {

        // Initialize Plugin Options
        $this->initOptions();

        // Initialize DB Tables used by the plugin
        $this->installDatabaseTables();

        // Other Plugin initialization - for the plugin writer to override as needed
        $this->otherInstall();

        // Record the installed version
        $this->saveInstalledVersion();

        // To avoid running install() more then once
        $this->markAsInstalled();
    }

    public function uninstall() {
        $this->otherUninstall();
        $this->unInstallDatabaseTables();
        $this->deleteSavedOptions();
        $this->markAsUnInstalled();
    }

    /**
     * Perform any version-upgrade activities prior to activation (e.g. database changes)
     * @return void
     */
    public function upgrade() {
    }

    public function activate() {
        if( !wp_next_scheduled( 'ctc_check_notices' ) ) {  
            wp_schedule_event( time(), 'twicedaily', 'ctc_check_notices' );
        }
    }

    public function deactivate() {
        // find out when the last event was scheduled
        $timestamp = wp_next_scheduled ('ctc_check_notices');
        // unschedule previous event if any
        wp_unschedule_event ($timestamp, 'ctc_check_notices');
    }

    protected function initOptions() {

    }

    public function addActionsAndFilters() {
    }

    protected function installDatabaseTables() {
    }

    protected function unInstallDatabaseTables() {
    }

    protected function otherInstall() {
    }

    protected function otherUninstall() {
    }

    /**
     * Puts the configuration page in the Plugins menu by default.
     * Override to put it elsewhere or create a set of submenus
     * Override with an empty implementation if you don't want a configuration page
     * @return void
     */
    public function addSettingsSubMenuPage() {
        $this->addSettingsSubMenuPageToPluginsMenu();
        //$this->addSettingsSubMenuPageToSettingsMenu();
    }


    protected function requireExtraPluginFiles() {
        require_once(ABSPATH . 'wp-includes/pluggable.php');
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }

    protected function addSettingsSubMenuPageToPluginsMenu() {
        $this->requireExtraPluginFiles();
        $displayName = $this->getPluginDisplayName();
        add_submenu_page('plugins.php',
                         $displayName,
                         $displayName,
                         'manage_options',
                         get_class($this) . 'Settings',
                         array(&$this, 'settingsPage'));
    }


    protected function addSettingsSubMenuPageToSettingsMenu() {
        $this->requireExtraPluginFiles();
        $displayName = $this->getPluginDisplayName();
        add_options_page($displayName,
                         $displayName,
                         'manage_options',
                         get_class($this) . 'Settings',
                         array(&$this, 'settingsPage'));
    }

    /**
     * @param  $name string name of a database table
     * @return string input prefixed with the Wordpress DB table prefix
     * plus the prefix for this plugin to avoid table name collisions
     */
    protected function prefixTableName($name) {
        global $wpdb;
        return $wpdb->prefix . $this->prefix($name);
    }


}
