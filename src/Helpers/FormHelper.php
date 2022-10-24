<?php

namespace App\Helpers;

use Symfony\Component\Form\FormBuilderInterface;

class FormHelper
{
    /**
     * @param FormBuilderInterface $builder
     * @param string $fieldName
     * @param string $optionName
     * @param $optionData
     */
    public static function setOptionToExistingFormField(
        &$builder,
        string $fieldName,
        string $optionName,
        $optionData
    ): void {
        if (!$builder->has($fieldName)) {
            // return or throw exception as you wish
            return;
        }

        $field = $builder->get($fieldName);

        // Get some things from the old field that we also need on the new field
        //$modelTransformers = $field->getModelTransformers();
        //$viewTransformers = $field->getViewTransformers();
        $options = $field->getOptions();
        $fieldType = get_class($field->getType()->getInnerType());

        // Now set the new option value
        $options[$optionName] = $optionData;

        /**
         * Just use "add" again, if it already exists the existing field is overwritten.
         * See the documentation of the add() function
         * Even the position of the field is preserved
         */
        $builder->add($fieldName, $fieldType, $options);

        // Reconfigure the transformers (if any), first remove them or we get some double
        //$newField = $builder->get($fieldName);
        /*
        $newField->resetModelTransformers();
        $newField->resetViewTransformers();
        foreach ($modelTransformers as $transformer) {
            $newField->addModelTransformer($transformer);
        }
        foreach ($viewTransformers as $transformer) {
            $newField->addViewTransformer($transformer);
        }
        */
    }
}
