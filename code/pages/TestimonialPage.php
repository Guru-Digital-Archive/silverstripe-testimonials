<?php

class TestimonialPage extends Page
{
    private static $db               = [
        'NotificationEmails' => 'Text',
        'ThankYouMessage'    => 'HTMLText'
    ];
    protected $NotificationEmailList = [];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->addFieldToTab('Root.Main', HtmlEditorField::create('ThankYouMessage', 'Thank you message'));
        $fields->addFieldToTab('Root.Notfications', TextareaField::create('NotificationEmails', 'Notification emails')
                        ->setDescription('Enter email accounts which will be notified when a new Testimonial is submitted. (One per line)'));
        return $fields;
    }

    public function Testimonials()
    {
        return Testimonial::get('Testimonial', ['Approved' => 1])->sort('SortOrder');
    }

    public function AllNotificationEmails()
    {
        return Testimonial::get('Testimonial', ['Approved' => 1])->sort('SortOrder');
    }

    public function GetNotificationEmailList()
    {
        if (!$this->NotificationEmailList) {
            $this->NotificationEmailList = new ArrayList();
            $addresses                   = preg_split("/\r\n|\n|\r/", $this->NotificationEmails);
            foreach ($addresses as $address) {
                $this->NotificationEmailList->add($address);
            }
        }
        return $this->NotificationEmailList;
    }
}

class TestimonialPage_Controller extends Page_Controller
{
    private static $allowed_actions = [
        'TestimonialForm', 'TestimonialSubmittedEmailPreview'
    ];

    public function TestimonialForm()
    {
        $fields = FieldList::create([
            TextField::create('Author', 'Your Name'),
            TextareaField::create('Content', 'Your testimonial')
        ]);

        $actions = FieldList::create(FormAction::create('submitTestimonial')
                        ->setUseButtonTag(true)
                        ->setTitle('Send')
                        ->setAttribute('data-style', 'expand-left')
                        ->addExtraClass('ladda-button'));
        $form    = Form::create($this, 'TestimonialForm', $fields, $actions);
        $this->extend('updateForm', $form);
        return $form;
    }

    public function validateSubmission($data, $form)
    {
        $isValid     = true;
        $filters     = [
            'Author'  => FILTER_SANITIZE_STRING,
            'Content' => FILTER_SANITIZE_STRING
        ];
        $dataFilterd = filter_var_array($data, $filters);
        if (!$dataFilterd['Author']) {
            $isValid = false;
            $form->addErrorMessage('Author', 'Please enter a valid name', 'bad');
        }
        if (!$dataFilterd['Content']) {
            $isValid = false;
            $form->addErrorMessage('Content', 'Please enter your testimonial', 'bad');
        }
        return $isValid ? $dataFilterd : false;
    }

    public function submitTestimonial($data, Form $form)
    {
        $dataFilterd = $this->validateSubmission($data, $form);
        $result      = [];
        if ($dataFilterd) {
            $this->extend('onBeforeSubmitTestimonial', $dataFilterd, $form);
            $testimonial  = Testimonial::create($dataFilterd);
            $testimonial->write();
            $extendResult = $this->extend('onAfterSubmitTestimonial', $this, $testimonial);
            if (is_array($extendResult) && count($extendResult)) {
                foreach ($extendResult as $value) {
                    if (!is_null($value)) {
                        if (!is_string($result)) {
                            $result = '';
                        }
                        $result = $value;
                    }
                }
            } else {
                $result['ShowForm'] = false;
                $result['Content']  = 'Your testimonial has been added';
            }
            $this->SendNotificationMail($testimonial);
        } else {
            $result = $this->redirectBack();
        }

        return $result;
    }

    /**
     *
     * @param Testimonial $testimonial
     */
    public function SendNotificationMail($testimonial)
    {
        $email = Email::create()
                ->setSubject('New testimonial has been submitted')
                ->setTemplate('TestimonialSubmitted')
                ->populateTemplate(ArrayData::create([
                    'Author'      => $testimonial->Author,
                    'Testimonial' => $testimonial->Content,
                    'Link'        => TestimonialAdmin::create()->Link('/Testimonial/EditForm/field/Testimonial/item/' . $testimonial->ID . '/edit')
        ]));

        foreach ($this->GetNotificationEmailList() as $emailAddress) {
            $email->setTo($emailAddress);
            $email->send();
        }
    }

    public function TestimonialSubmittedEmailPreview()
    {
        return $this->customise(
                        [
                            'Author'      => 'John Doe',
                            'Textimonial' => 'This is a testimonial',
                            'Link'        => Director::absoluteURL('admin/Testimonials/Testimonial/EditForm/field/Testimonial/item/2/edit'),
                        ]
                )->renderWith('TestimonialSubmitted');
    }
}
