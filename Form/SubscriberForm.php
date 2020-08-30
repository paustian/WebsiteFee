<?php
namespace Paustian\WebsiteFeeModule\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
/**
 * Description of ExamForm
 * Set up the elements for a Exam form.
 *
 * @author paustian
 * 
 */
class SubscriberForm extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
            ->add('wsfsubsname', TextType::class, array('label' => 'Subscription Name', 'required' => true))
            ->add('wsfitem', IntegerType::class, array('label' => 'Subscription Item Number', 'required' => true))
            ->add('wsfpaymentamount', IntegerType::class, array('label' => 'Payment Amount', 'required' => true))
            ->add('wsfemail', EmailType::class, array('label' => 'Provider Email', 'required' => true))
            ->add('wsfgroupid', IntegerType::class, array('label' => 'The group to subscribe', 'required' => true))
            ->add('save', SubmitType::class, array('label' => 'Save Subscription'))
            ->add('cancel', ButtonType::class, array('label' => 'Cancel'));
        
    }

    public function getBlockPrefix() : string
    {
        return 'paustianwebsitefeemodule_subscriberform';
    }

    /**
     * OptionsResolver is @deprecated and is supposed to be replaced by
     * OptionsResolver but docs not clear on implementation
     *
     * @param OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolver $resolver) : void
    {
        $resolver->setDefaults(array(
            'data_class' => 'Paustian\WebsiteFeeModule\Entity\WebsiteFeeSubsEntity',
        ));
    }
}

