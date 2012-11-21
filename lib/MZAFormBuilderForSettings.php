<?php

if (!class_exists("MZAFormBuilder")){
    include_once "MZAFormBuilder.php";
}

if (!class_exists("MZAFormBuilderForSettings")){
 
    class MZAFormBuilderForSettings extends MZAFormBuilder {

        public function __construct($name, $title, $sections, $changes_listener=null){
            parent::__construct($name, $title, $sections, $changes_listener);

        }

        protected function form_head(){
            echo '<div class="wrap">';
            echo '<h2>'. $this->title .'</h2>';
            echo '<form id="'. $this->name .'_form" name="'. $this->name .'_form" method="post" action="">';
        }

        protected function form_footer(){
            echo '</form>';
            echo '</div>';
        }

        protected function form_submit(){
            echo "<p class='submit'>";
            echo "<input type='submit' id='submit' name='submit' class='button-primary' value='" . __("Save Changes") . "'>";
            echo "</p>";
        }

        protected function section_head($key, $section){
            if (isset($section["title"]) && $section["title"] != ""){
                echo '<h3 class="title">' . $section["title"] . '</h3>';
            }

            if (isset($section["description"]) && $section["description"] != ""){
                echo '<p>';
                    echo $section["description"];
                echo '</p>';
            }

            do_action("MZAFormBuilder_section_head", $this->name, $key );

            echo '<table class="form-table" id="section-'. $key .'" class="section">';
            echo '<tbody>';
        }

        protected function section_footer($key, $section){
            echo '</tbody>';
            echo '</table>';
        }


        protected function do_field($name, $field){

            if (method_exists($this, "do_field_" . strtolower($field["type"]) )){

                echo '<tr valign="top" id="field-row-'. $name .'" class="field-row field-row-'.$field["type"].'">';
                    echo '<th scope="row">';
                        echo '<label for="'.$name.'">' . $field["title"]  . '</label>';
                    echo '</th>';
                    echo '<td>';
                        call_user_func(array($this, "do_field_" . strtolower($field["type"])), $name, $field);

                        if ( isset($field["description"]) && $field["description"] != "" ){
                            echo "&nbsp;&nbsp;<span class='description'>" . $field["description"] . "</span>";
                        }

                    echo '</td>';
                echo '</tr>';
            }

        }

    }

}