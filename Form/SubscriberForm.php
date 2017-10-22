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
use Zikula\Common\Translator\TranslatorInterface;
/**
 * Description of ExamForm
 * Set up the elements for a Exam form.
 *
 * @author paustian
 * 
 */
class SubscriberForm extends AbstractType {
    /**
     * @var TranslatorInterface
     */
    private $translator;


    /**
     * BlockType constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(
        TranslatorInterface $translator)   {
        $this->translator = $translator;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('wsfsubsname', TextType::class, array('label' => $this->translator->__('Subscription Name'), 'required' => true))
            ->add('wsfitem', IntegerType::class, array('label' => $this->translator->__('Subscription Item Number'), 'required' => true))
            ->add('wsfpaymentamount', IntegerType::class, array('label' => $this->translator->__('Payment Amount'), 'required' => true))
            ->add('wsfemail', EmailType::class, array('label' => $this->translator->__('Provider Email'), 'required' => true))
            ->add('wsfgroupid', IntegerType::class, array('label' => $this->translator->__('The group to subscribe'), 'required' => true))
            ->add('save', SubmitType::class, array('label' => $this->translator->__('Save Subscription')))
            ->add('cancel', ButtonType::class, array('label' => $this->translator->__('Cancel')));
        
    }

    public function getBlockPrefix()
    {
        return 'paustianwebsitefeemodule_subscriberform';
    }

    /**
     * OptionsResolver is @deprecated and is supposed to be replaced by
     * OptionsResolver but docs not clear on implementation
     *
     * @param OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Paustian\WebsiteFeeModule\Entity\WebsiteFeeSubsEntity',
        ));
    }
}

