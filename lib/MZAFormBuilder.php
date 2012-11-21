<?php

if (!class_exists("MZAFormBuilder")){

    abstract class MZAFormBuilder {

        protected $sections;
        protected $title;
        protected $name;
        protected $changes_listener;

        function __construct($name, $title, $sections, $changes_listener){
            $this->title = $title;
            $this->name = $name;
            $this->sections = $sections;
            $this->changes_listener = $changes_listener;
        }

        /* for implementation in childs */
        abstract protected function form_head();
        abstract protected function form_submit();
        abstract protected function form_footer();
        abstract protected function section_head($key, $section);
        abstract protected function section_footer($key, $section);
        abstract protected function do_field($name, $field);

        private function maybe_process_form(){

            if (!empty($_POST)){

                if ( isset($_POST[$this->name . "_nonce"]) ){

                    if ( !wp_verify_nonce($_POST[$this->name . "_nonce"], $this->name ) )
                        wp_die( 'Cheatin&#8217; uh?' );

                    if ( !check_admin_referer( $this->name, $this->name . "_nonce" ) )
                        wp_die( 'Cheatin&#8217; uh?' );

                    foreach($this->sections as $key_section => $section){
                        foreach ($section["fields"] as $key_field => $field){
                            if ( isset($_POST[$this->field_name($key_field, $key_section)]) ){
                                $this->sections[$key_section]["fields"][$key_field]["value"] = $_POST[$this->field_name($key_field, $key_section)];
                            }else{
                                if ( isset($this->sections[$key_section]["fields"][$key_field]["value"]) ){
                                    $this->sections[$key_section]["fields"][$key_field]["value"] = "";
                                }
                            }
                        }
                    }

                    if (is_array($this->changes_listener)){
                        if ( method_exists($this->changes_listener[0], $this->changes_listener[1]) ){
                            call_user_func_array($this->changes_listener, array($this->sections));
                        }
                    }
                }
            }
        }



        public function do_form(){



            $this->maybe_process_form();


            $sections = $this->sections;

            $this->form_head();

            wp_nonce_field($this->name, $this->name . "_nonce", true, true);

            if (!empty($sections)){
                foreach($sections as $key => $section){
                    $this->do_section($key, $section);
                }
            }
            $this->form_submit();

            $this->form_footer();
        }

        protected function do_section($key, $section){
            $this->section_head($key, $section);
            if ( isset($section["fields"]) && !empty($section["fields"]) ){
                foreach ($section["fields"] as $name => $field){
                    $this->do_field ($this->field_name($name, $key), $field);
                }
            }
            $this->section_footer($key, $section);
        }


        protected function field_name($name, $section ){
            return $this->name . "_" . $section . "_" . $name;
        }

        /* Fields */

        protected function do_field_text($name, $field){
            echo '<input type="text" name="' . $name .'" value="'.esc_attr(isset($field["value"]) ? $field["value"] : "").'" class="regular-text" />';
        }

        protected function do_field_password($name, $field){
            echo '<input type="password" name="' . $name .'" value="'.esc_attr(isset($field["value"]) ? $field["value"] : "").'" class="regular-text" />';
        }

        protected function do_field_textarea($name, $field){
            echo '<textarea name="' . $name .'" cols="57" rows="10">'.esc_textarea(isset($field["value"]) ? $field["value"] : "").'</textarea>';
        }

        protected function do_field_checkbox($name, $field){

           if (isset($field["options"]) && is_array($field["options"])){
               foreach ($field["options"] as $value => $label ){
                   echo '<input type="checkbox" '. checked(isset($field["value"]) ? $field["value"] : "", $value, false) .' name="' . $name .'" value="'.$value.'"/>&nbsp;<span>' . $label . '</span>';
               }
            }
        }

        protected function do_field_radio($name, $field){
            if (isset($field["options"]) && is_array($field["options"])){
                foreach ($field["options"] as $value => $label ){

                    echo '<input type="radio" '. checked(isset($field["value"]) ? $field["value"] : "", $value, false) .' name="' . $name .'" value="'.$value.'"/>&nbsp;<span>' . $label . '</span><br/>';
                }
             }
         }

        protected function do_field_user($name, $field){
            $query_args = array();
            if (isset($field["role"])){
                $query_args["role"] = $field["role"];
            }
            $users = get_users($query_args);

            echo '<select name="'.$name.'">';
            foreach($users as $user){
                echo '<option '. selected(isset($field["value"]) ? $field["value"] : "", $user->ID, false) .' value="'. $user->ID .'">' . $user->display_name . '</option>';
            }
            echo '</select>';
        }

        protected function do_field_role($name, $field){
            $wp_roles = new WP_Roles();
            echo '<select name="'.$name.'">';
            foreach ( $wp_roles->role_names as $role => $role_name ) {
                echo '<option '. selected(isset($field["value"]) ? $field["value"] : "", $role, false)  .' value="'. $role .'">' . $role_name . '</option>';
            }
            echo '</select>';
        }

        protected function do_field_time($name, $field){
            echo '<input type="text" name="' . $name .'" value="'.esc_attr(isset($field["value"]) ? $field["value"] : "").'" size="5" />';
        }

        protected function do_field_support($name, $field){
            echo '<a href="mailto:'.esc_attr(isset($field["email"]) ? $field["email"] : "").'">'.esc_attr(isset($field["email"]) ? $field["email"] : "").'</a>';
         }
    }
}
?>