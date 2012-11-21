<?php

if (!class_exists("MZAFormBuilderForSettings")){
    include_once "MZAFormBuilderForSettings.php";
}

if (!class_exists("MZASettings")){

    class MZASettings {

        private $owner;
        private $location;
        private $sections = array();
        private $page;

        private $settings;

        private $builder;


        public $settingsPageTitle = "Settings";
        public $settingsLinkTitle = "Settings";
        public $customJS = "";
        public $customCSS = "";


        public function __construct($owner, $location, $sections){
            $this->owner = $owner;
            $this->location = $location;
            $this->sections = $sections;

            $this->set_defaults();
            $this->load();

            add_action('admin_menu', array($this, 'settings_submenu'));

        }


        public function admin_head(){

            if ($this->customJS != ""){
                echo "<script type='text/javascript'>";
                echo "jQuery(document).ready(function($) {";
                echo $this->customJS;
                echo "});";
                echo "</script>";
            }

            if ($this->customCSS != ""){
                echo "<style type='text/css'>";
                echo $this->customCSS;
                echo "</style>";
            }
        }

        public function get_setting($group, $field){
            return $this->settings[$group][$field];
        }

        public function settings_submenu() {
            $this->page = add_submenu_page( $this->location , $this->settingsPageTitle, $this->settingsLinkTitle, 'manage_options', $this->owner . '-settings', array($this, 'settings_page') );
            add_action('admin_head-'.$this->page, array($this, 'admin_head'),100);
        }

        public function load(){
            $this->settings = get_option($this->owner."_settings");
            $this->settings = apply_filters($this->owner . "_settings_after_load", $this->settings);
            $this->fill_sections_from_settings();
        }

        public function save(){
            $this->fill_settings_from_sections();
            $this->settings = apply_filters($this->owner . "_settings_before_save", $this->settings);
            update_option($this->owner."_settings", $this->settings);
        }

        private function set_defaults(){

            $settings = get_option($this->owner."_settings");

            if (!$settings || empty($settings)){
                $sections = $this->sections;
                foreach ( $sections as $key1 => $section){
                    foreach ($section["fields"] as $key2 => $field){
                        $value = ""; // if there is no setting and no default, use "", the ultimate default.
                        if (isset($this->settings[$key1][$key2])){ //settings have priority. Notice that i'm checking for set, not empty (nothing wrong with an empty setting, don't want to overwrite with default)
                            $value = $this->settings[$key1][$key2];
                        }elseif (isset($field["default"])){ //if there is nothing set (fist time only for this particular field) use the default
                            $value = $field["default"];
                        }
                        $this->settings[$key1][$key2] = $value;
                        $sections[$key1]["fields"][$key2]["value"] = $value;
                    }
                }
                $this->sections = $sections;
                update_option($this->owner."_settings", $this->settings);
            }
        }

        private function fill_settings_from_sections(){
            foreach ( $this->sections as $key1 => $section){
                foreach ($section["fields"] as $key2 => $field){
                    $value = "";
                    if (isset( $field["value"] )){
                        $value = $field["value"];
                    }
                    $this->settings[$key1][$key2] = $value;
                }
            }
        }

        private function fill_sections_from_settings(){
            $sections = $this->sections;
            foreach ( $sections as $key1 => $section){
                foreach ($section["fields"] as $key2 => $field){
                    $value = "";
                    if (isset( $this->settings[$key1][$key2] )){
                        $value = $this->settings[$key1][$key2];
                    }
                    $sections[$key1]["fields"][$key2]["value"] = $value;
                }
            }
            $this->sections = $sections;
        }

        public function settings_page(){
            $this->load();
            $name = $this->owner . "_settings";
            $this->builder = new MZAFormBuilderForSettings($name, $this->settingsPageTitle, $this->sections, array($this, "update_sections"));
            $this->builder->do_form();
        }

        public function update_sections($sections){
            $this->sections = $sections;
            $this->save();
        }
    }
}
