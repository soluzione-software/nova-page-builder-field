<?php

namespace DigitalCloud\PageBuilderField;

use Illuminate\Support\Arr;
use Laravel\Nova\Fields\Expandable;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class PageBuilderField extends Field
{
    use Expandable;

    protected $cssAttribute = null;
    protected $htmlAttribute = null;
    protected $jsAttribute = null;

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'page-builder-field';

    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);
        $this->hideFromIndex();
    }

    public function css($attribute)
    {
        $this->cssAttribute = $attribute;
        return $this;
    }

    public function html($attribute)
    {
        $this->htmlAttribute = $attribute;
        return $this;
    }

    public function js($attribute)
    {
        $this->jsAttribute = $attribute;
        return $this;
    }

    /**
     * Resolve the given attribute from the given resource.
     *
     * @param  mixed  $resource
     * @param  string  $attribute
     * @return mixed
     */
    protected function resolveAttribute($resource, $attribute)
    {
        $html = data_get($resource, str_replace('->', '.', !is_null($this->htmlAttribute) ? $this->htmlAttribute : $attribute));
        $css = !is_null($this->cssAttribute) ? data_get($resource, str_replace('->', '.', $this->cssAttribute)) : '';
        $js = !is_null($this->jsAttribute) ? data_get($resource, str_replace('->', '.', $this->jsAttribute)) : '';

        return $css . $html . $js;
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param NovaRequest $request
     * @param  string  $requestAttribute
     * @param  object  $model
     * @param  string  $attribute
     * @return mixed
     */
    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        if ($request->exists($requestAttribute)) {

            $value = json_decode($request[$requestAttribute], true);

            $html = $value['html'];
            $css = $value['css'];
            $js = $value['js'];

            if (is_null($this->cssAttribute))
                $model->{$attribute} .= $css;
            else
                $model->{$this->cssAttribute} = $css;

            if (is_null($this->htmlAttribute))
                $model->{$attribute} .= $html;
            else
                $model->{$this->htmlAttribute} = $html;

            if (is_null($this->jsAttribute))
                $model->{$attribute} .= $js;
            else
                $model->{$this->jsAttribute} = $js;

        }
    }

    /**
     * Prepare the element for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'shouldShow' => $this->shouldBeExpanded(),
        ]);
    }

}
