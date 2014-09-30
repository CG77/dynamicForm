<?php

namespace Novactive\EzPublishFormGeneratorBundle\Form\Type\Generator;

use Symfony\Component\Form\Extension\Core\Type\BaseType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotNull;

class ConstraintType extends BaseType
{

    protected $attribute_id = null;
    protected $type = "";
    protected $constraints = array();

    public function __construct($aConstraints, $sType, $iAttributeId)
    {
        $this->attribute_id = $iAttributeId;
        $this->constraints = $aConstraints;
        $this->type = $sType;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'label' => ' '
            )
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->constraints as $sConstraints) {
            switch ($sConstraints) {
                case 'join_as_attachment' :
                    $builder->add(
                        "join_as_attachment",
                        "checkbox",
                        array(
                            "required" => false,
                            'label' => "Joindre au mail agent ?"
                        )
                    );
                    break;
                case 'hidden' :
                    $builder->add(
                        "hidden",
                        "checkbox",
                        array(
                            "required" => false
                        )
                    );
                    break;
                case 'max_length':
                    $builder->add(
                        "max_length",
                        "integer",
                        array(
                            "required" => false,
                            "label" => "Taille maximale",
                            "invalid_message" => "Max Length => Cette valeur doit contenir un entier"
                        )
                    );
                    break;
                case 'nb_line':
                    $builder->add(
                        "nb_line",
                        "integer",
                        array(
                            "required" => false,
                            "label" => "Nombre de ligne",
                            "invalid_message" => "Nombre de ligne => Cette valeur doit contenir un entier"
                        )
                    );
                    break;
                case 'nb_column':
                    $builder->add(
                        "nb_column",
                        "integer",
                        array(
                            "required" => false,
                            "label" => "Nombre de colonne",
                            "invalid_message" => "Nombre de colonne => Cette valeur doit contenir un entier"
                        )
                    );
                    break;
                case 'select_type':
                    $builder->add(
                        "select_type",
                        "choice",
                        array(
                            "choices" => array(
                                "select" => " Liste déroulante à choix unique ",
                                "multiple" => " Liste déroulante à choix multiple ",
                                "checkbox" => " Réponse à choix multiples ",
                                "radio" => " Réponse à choix unique "
                            ),
                            "label" => "Type de sélection",
                            'empty_value' => false,
                            "required" => false,
                            "expanded" => true,
                            "multiple" => false,
                            'attr' => array('class' => 'clearfix padding-select')
                        )
                    );
                    break;
            }
        }
    }

    public function getName()
    {
        return "attribute_" . $this->type . "_" . $this->attribute_id;
    }
}