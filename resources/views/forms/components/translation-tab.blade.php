@php
    $id = $getId();
    $isContained = $getContainer()
        ->getParentComponent()
        ->isContained();
    $isVertical = $getContainer()
        ->getParentComponent()
        ->isVertical();
    $activeTabClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-active',
        'p-6' => $isContained,
        'mt-6' => ! $isContained,
    ]);

    $inactiveTabClasses = 'hidden h-0 overflow-hidden p-0';

    $actions = $getActions();
    $hasActions = filled($actions);

    $locale = $getLocale() ?? $id;
@endphp

<div
    x-bind:class="{
        @js($activeTabClasses): tab === @js($id),
        @js($inactiveTabClasses): tab !== @js($id),
    }"
    x-on:expand="tab = @js($id)"
    {{
        $attributes
            ->merge(
                [
                    'aria-labelledby' => $id,
                    'id' => $id,
                    'role' => 'tabpanel',
                    'tabindex' => '0',
                    'wire:key' =>
                        "{$this->getId()}.{$getStatePath()}." .
                        \Webard\FilamentTranslatable\Forms\Component\Translations::class .
                        ".tabs.{$id}",
                ],
                escape: false,
            )
            ->merge($getExtraAttributes(), escape: false)
            ->class(['fi-sc-tabs-tab'])
    }}
>
    <div>
        {{ $getChildComponentContainer() }}
    </div>
</div>
