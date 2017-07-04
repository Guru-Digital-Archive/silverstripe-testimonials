<?php

class TestimonialWidget extends Widget
{

    private static $include_js = true;
    private static $db         = [
        'BtnText' => 'VarChar(120)'
    ];
    private static $has_one    = [
        'MoreLink' => 'Page'
    ];

    public function Title()
    {
        return $this->WidgetLabel;
    }

    public function Testimonials()
    {
        $result = ArrayList::create();
        $this->extend('updateForm', $result);
        if (!$result->count()) {
            $result = Testimonial::get('Testimonial', ['Approved' => 1], 'RAND()');
        }
        if ($this->config()->include_js) {
            Requirements::javascript(FRAMEWORK_DIR . '/thirdparty/jquery/jquery.js');
            Requirements::javascript('silverstripe-testimonials/javascript/jquery.anyslider.min.js');
            Requirements::javascript('silverstripe-testimonials/javascript/main.js');
        }
        return $result;
    }

    public function getCMSFields()
    {
        $fields     = parent::getCMSFields();
        $dataFields = $fields->dataFields();
        foreach ($dataFields as $dataField) {
            $fields->remove($dataField);
        }
        $tabs = TabSet::create('Root', Tab::create('Main'));
        $fields->add($tabs);
        $fields->addFieldsToTab("Root.Main", $dataFields);
        $fields->addFieldToTab('Root.Main', TextField::create('WidgetLabel', 'Widget Label'), 'Enabled');
        $fields->addFieldToTab('Root.Main', TextField::create('WidgetName', 'Widget Name'), 'Enabled');
        $fields->addFieldToTab('Root.Main', TextField::create('BtnText', 'Button label'), 'Enabled');
        $fields->addFieldToTab('Root.Main', TreeDropdownField::create('MoreLinkID', 'Button link', 'SiteTree'), 'Enabled');
        return $fields;
    }
}
