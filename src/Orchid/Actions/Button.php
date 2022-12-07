<?php

namespace Khamsolt\Orchid\Files\Orchid\Actions;

use Orchid\Screen\Action;

/**
 *  *
 * @method Button name(string $name = null)
 * @method Button icon(string $icon = null)
 * @method Button class(string $classes = null)
 * @method Button parameters(array|object $name)
 * @method Button disabled(bool $disabled)
 */
class Button extends Action
{
    protected $view = 'orchid-files::actions.button';

    /**
     * Default attributes value.
     *
     * @var array
     */
    protected $attributes = [
        'class' => 'btn btn-link',
        'novalidate' => false,
        'icon' => null,
        'action' => null,
        'parameters' => [],
        'turbo' => true,
        'form' => 'post-form',
    ];

    /**
     * Attributes available for a particular tag.
     *
     * @var array
     */
    protected $inlineAttributes = [
        'form',
        'formaction',
        'formenctype',
        'formmethod',
        'formnovalidate',
        'formtarget',
        'autofocus',
        'tabindex',
    ];
}
