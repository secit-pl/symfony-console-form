<?php

namespace Matthias\SymfonyConsoleForm\Bridge\Transformer;

use Doctrine\Common\Collections\Collection;
use Matthias\SymfonyConsoleForm\Console\Formatter\Format;
use Matthias\SymfonyConsoleForm\Form\FormUtil;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Reusable code for FormToQuestionTransformers.
 */
abstract class AbstractTransformer implements FormToQuestionTransformer
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    protected function questionFrom(FormInterface $form): string
    {
        $question = $this->translator->trans(FormUtil::label($form), [], $this->translationDomainFrom($form));

        return $this->formattedQuestion($question, $this->defaultValueFrom($form));
    }

    /**
     * @return mixed
     */
    protected function defaultValueFrom(FormInterface $form)
    {
        $defaultValue = $form->getData();
        if (is_array($defaultValue)) {
            $defaultValue = implode(',', $defaultValue);
        } elseif ($defaultValue instanceof Collection) {
            $defaultValue = implode(',', $defaultValue->toArray());
        }

        return $defaultValue;
    }

    protected function translationDomainFrom(FormInterface $form): ?string
    {
        while ((null === $domain = $form->getConfig()->getOption('translation_domain')) && $form->getParent()) {
            $form = $form->getParent();
        }

        return $domain;
    }

    /**
     * @param mixed $defaultValue
     * @return string
     */
    protected function formattedQuestion(string $question, $defaultValue): string
    {
        return Format::forQuestion($question, $defaultValue);
    }
}
