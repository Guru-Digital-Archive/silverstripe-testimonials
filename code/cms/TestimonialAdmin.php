<?php

class TestimonialAdmin extends ModelAdmin
{
    private static $managed_models = ['Testimonial'];
    private static $url_segment    = 'Testimonials';
    private static $menu_title     = 'Testimonials';

    public function getEditForm($id = null, $fields = null)
    {
        $form      = parent::getEditForm($id, $fields);
        $gridField = $form->Fields()->dataFieldByName($this->sanitiseClassName($this->modelClass));
        //This check is simply to ensure you are on the managed model you want adjust accordingly
        if ($this->modelClass == 'Testimonial' && $gridField instanceof GridField) {
            $gridField->getConfig()->addComponent(new GridFieldOrderableRows('SortOrder'));
        }

        return $form;
    }
}
