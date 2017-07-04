<?php

class Testimonial extends DataObject
{

    private static $db                = [
        'Content'   => 'Text',
        'Author'    => 'Text',
        'Approved'  => 'Boolean',
        'SortOrder' => 'Int'
    ];
    private static $searchable_fields = [
        'Content',
        'Author',
        'Approved'
    ];
    private static $summary_fields    = [
        'Created'       => 'Created',
        'Author'        => 'Author',
        'Content'       => 'Content',
        'Approved.Nice' => 'Approved'
    ];
    private static $default_sort      = 'Created DESC';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('SortOrder');
        return $fields;
    }

    public function forTemplate()
    {
        return $this->renderWith('Testimonial');
    }

    protected function onBeforeWrite()
    {
        if (!$this->SortOrder) {
            $this->SortOrder = $this::get()->max('SortOrder') + 1;
        }
        return parent::onBeforeWrite();
    }
}
