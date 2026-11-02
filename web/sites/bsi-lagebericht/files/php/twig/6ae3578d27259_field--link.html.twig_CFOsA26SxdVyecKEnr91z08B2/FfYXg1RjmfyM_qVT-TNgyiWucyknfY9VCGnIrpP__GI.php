<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\MacroNamespace;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Sandbox\SecurityNotAllowedTestError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* field--link.html.twig */
class __TwigTemplate_ded936e5520ce6e54dcb4fce8f84cccc extends Template
{
    private Source $source;
    /**
     * @var array<string, MacroNamespace>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        $this->sandbox = $env->getExtension(SandboxExtension::class)->getChecker();
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'content' => [$this, 'block_content'],
            'label' => [$this, 'block_label'],
            'items' => [$this, 'block_items'],
            'item' => [$this, 'block_item'],
        ];
    }

    protected function doGetParent(array $context): bool|string|Template|TemplateWrapper
    {
        // line 60
        return $this->parent ??= $this->load("field.html.twig", 60);
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 63
        $context["modifiers"] = ((CoreExtension::getAttribute($this->env, $this->source, ($context["element"] ?? null), "#modifiers", [], "array", true, true, true, 63)) ? (Twig\Extension\CoreExtension::default((($_v0 = ($context["element"] ?? null)) && is_array($_v0) || $_v0 instanceof ArrayAccess && in_array($_v0::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v0["#modifiers"] ?? null) : CoreExtension::getAttribute($this->env, $this->source, ($context["element"] ?? null), "#modifiers", [], "array", false, false, true, 63)), [])) : ([]));
        // line 64
        $context["extra_classes"] = ((CoreExtension::getAttribute($this->env, $this->source, ($context["element"] ?? null), "#extra_classes", [], "array", true, true, true, 64)) ? (Twig\Extension\CoreExtension::default((($_v1 = ($context["element"] ?? null)) && is_array($_v1) || $_v1 instanceof ArrayAccess && in_array($_v1::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v1["#extra_classes"] ?? null) : CoreExtension::getAttribute($this->env, $this->source, ($context["element"] ?? null), "#extra_classes", [], "array", false, false, true, 64)), [])) : ([]));
        // line 65
        $context["icon"] = (((CoreExtension::getAttribute($this->env, $this->source, ($context["element"] ?? null), "#icon", [], "array", true, true, true, 65) &&  !(null === (($_v2 = ($context["element"] ?? null)) && is_array($_v2) || $_v2 instanceof ArrayAccess && in_array($_v2::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v2["#icon"] ?? null) : CoreExtension::getAttribute($this->env, $this->source, ($context["element"] ?? null), "#icon", [], "array", false, false, true, 65))))) ? ((($_v3 = ($context["element"] ?? null)) && is_array($_v3) || $_v3 instanceof ArrayAccess && in_array($_v3::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v3["#icon"] ?? null) : CoreExtension::getAttribute($this->env, $this->source, ($context["element"] ?? null), "#icon", [], "array", false, false, true, 65))) : (null));
        // line 66
        $context["icon_position"] = ((CoreExtension::getAttribute($this->env, $this->source, ($context["element"] ?? null), "#icon_position", [], "array", true, true, true, 66)) ? (Twig\Extension\CoreExtension::default((($_v4 = ($context["element"] ?? null)) && is_array($_v4) || $_v4 instanceof ArrayAccess && in_array($_v4::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v4["#icon_position"] ?? null) : CoreExtension::getAttribute($this->env, $this->source, ($context["element"] ?? null), "#icon_position", [], "array", false, false, true, 66)), null)) : (null));
        // line 67
        $context["add_on"] = ((CoreExtension::getAttribute($this->env, $this->source, ($context["element"] ?? null), "#add_on", [], "array", true, true, true, 67)) ? (Twig\Extension\CoreExtension::default((($_v5 = ($context["element"] ?? null)) && is_array($_v5) || $_v5 instanceof ArrayAccess && in_array($_v5::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v5["#add_on"] ?? null) : CoreExtension::getAttribute($this->env, $this->source, ($context["element"] ?? null), "#add_on", [], "array", false, false, true, 67)), null)) : (null));
        // line 69
        $context["base_class"] = "bsi-link";
        // line 60
        $this->parent = $this->load("field.html.twig", 60);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["element", "label_hidden", "fully_inline", "title_attributes", "title_classes", "label", "items", "m"]);    }

    // line 71
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 72
        yield "  ";
        yield from $this->unwrap()->yieldBlock('label', $context, $blocks);
        // line 81
        yield "
  ";
        // line 82
        yield from $this->unwrap()->yieldBlock('items', $context, $blocks);
        return; yield;
    }

    // line 72
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_label(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 73
        yield "  ";
        if ( !(($tmp = ($context["label_hidden"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 74
            if ((($tmp = ($context["fully_inline"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 75
                yield "<span";
                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["title_attributes"] ?? null), "addClass", [($context["title_classes"] ?? null)], "method", false, false, true, 75), "html", null, true);
                yield ">";
                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["label"] ?? null), "html", null, true);
                yield ":</span>";
            } else {
                // line 77
                yield "<div";
                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["title_attributes"] ?? null), "addClass", [($context["title_classes"] ?? null)], "method", false, false, true, 77), "html", null, true);
                yield ">";
                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["label"] ?? null), "html", null, true);
                yield "</div>";
            }
        }
        // line 80
        yield "  ";
        return; yield;
    }

    // line 82
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_items(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 83
        yield "    ";
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["items"] ?? null));
        $context['loop'] = [
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        ];
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
            // line 84
            yield "      ";
            yield from $this->unwrap()->yieldBlock('item', $context, $blocks);
            // line 108
            yield "    ";
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['revindex0'], $context['loop']['revindex'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent);
        $context += $_parent;
        // line 109
        yield "  ";
        return; yield;
    }

    // line 84
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_item(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 85
        yield "        ";
        $context["url"] = (($_v6 = CoreExtension::getAttribute($this->env, $this->source, ($context["item"] ?? null), "content", [], "any", false, false, true, 85)) && is_array($_v6) || $_v6 instanceof ArrayAccess && in_array($_v6::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v6["#url"] ?? null) : CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["item"] ?? null), "content", [], "any", false, false, true, 85), "#url", [], "array", false, false, true, 85));
        // line 86
        yield "        ";
        $context["link_type"] = ((((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["url"] ?? null), "isRouted", [], "method", false, false, true, 86)) && $tmp instanceof Markup ? (string) $tmp : $tmp) && (CoreExtension::getAttribute($this->env, $this->source, ($context["url"] ?? null), "getRouteName", [], "method", false, false, true, 86) == "media_entity_download.download"))) ? ("download") : ((((($tmp = CoreExtension::getAttribute($this->env, $this->source,         // line 88
($context["url"] ?? null), "isExternal", [], "method", false, false, true, 88)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("external") : ("internal"))));
        // line 90
        yield "        ";
        // line 91
        yield "        ";
        if (((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["url"] ?? null), "isRouted", [], "method", false, false, true, 91)) && $tmp instanceof Markup ? (string) $tmp : $tmp) && CoreExtension::inFilter(CoreExtension::getAttribute($this->env, $this->source, ($context["url"] ?? null), "getRouteName", [], "method", false, false, true, 91), ["<nolink>", "<button>"]))) {
            // line 92
            yield "          ";
            $context["link_type"] = (((CoreExtension::getAttribute($this->env, $this->source, ($context["url"] ?? null), "getRouteName", [], "method", false, false, true, 92) == "<nolink>")) ? ("span") : ("button"));
            // line 93
            yield "        ";
        }
        // line 94
        yield "
        ";
        // line 95
        yield from $this->load("@bsi_bund/elements/link.html.twig", 95)->unwrap()->yield(CoreExtension::toArray(["base_class" =>         // line 96
($context["base_class"] ?? null), "url" => (($_v7 = CoreExtension::getAttribute($this->env, $this->source,         // line 97
($context["item"] ?? null), "content", [], "any", false, false, true, 97)) && is_array($_v7) || $_v7 instanceof ArrayAccess && in_array($_v7::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v7["#url"] ?? null) : CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["item"] ?? null), "content", [], "any", false, false, true, 97), "#url", [], "array", false, false, true, 97)), "title" => (($_v8 = CoreExtension::getAttribute($this->env, $this->source,         // line 98
($context["item"] ?? null), "content", [], "any", false, false, true, 98)) && is_array($_v8) || $_v8 instanceof ArrayAccess && in_array($_v8::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v8["#title"] ?? null) : CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["item"] ?? null), "content", [], "any", false, false, true, 98), "#title", [], "array", false, false, true, 98)), "attributes" => CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source,         // line 99
($context["item"] ?? null), "attributes", [], "any", false, false, true, 99), "merge", [$this->extensions['Drupal\Core\Template\TwigExtension']->createAttribute(((CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source,         // line 100
($context["item"] ?? null), "content", [], "any", false, true, true, 100), "#options", [], "array", false, true, true, 100), "attributes", [], "array", true, true, true, 100)) ? (Twig\Extension\CoreExtension::default((($_v9 = (($_v10 = CoreExtension::getAttribute($this->env, $this->source, ($context["item"] ?? null), "content", [], "any", false, false, true, 100)) && is_array($_v10) || $_v10 instanceof ArrayAccess && in_array($_v10::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v10["#options"] ?? null) : CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["item"] ?? null), "content", [], "any", false, false, true, 100), "#options", [], "array", false, false, true, 100))) && is_array($_v9) || $_v9 instanceof ArrayAccess && in_array($_v9::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v9["attributes"] ?? null) : CoreExtension::getAttribute($this->env, $this->source, (($_v11 = CoreExtension::getAttribute($this->env, $this->source, ($context["item"] ?? null), "content", [], "any", false, false, true, 100)) && is_array($_v11) || $_v11 instanceof ArrayAccess && in_array($_v11::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v11["#options"] ?? null) : CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["item"] ?? null), "content", [], "any", false, false, true, 100), "#options", [], "array", false, false, true, 100)), "attributes", [], "array", false, false, true, 100)), [])) : ([])))], "method", false, false, true, 99), "addClass", [Twig\Extension\CoreExtension::map($this->env, $this->env->hasExtension(\Twig\Extension\SandboxExtension::class) && $this->env->getExtension(\Twig\Extension\SandboxExtension::class)->getChecker()->isSandboxed($this->source),         // line 101
($context["modifiers"] ?? null), function ($__m__) use ($context, $macros) { $context["m"] = $__m__; return ((($context["base_class"] ?? null) . "-") . ($context["m"] ?? null)); })], "method", false, false, true, 100), "addClass", [        // line 102
($context["extra_classes"] ?? null)], "method", false, false, true, 101), "link_type" =>         // line 103
($context["link_type"] ?? null), "icon" =>         // line 104
($context["icon"] ?? null), "icon_position" =>         // line 105
($context["icon_position"] ?? null)]));
        // line 107
        yield "      ";
        return; yield;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "field--link.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDefaultEscapeStrategy(): string|false
    {
        return "html";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  209 => 107,  207 => 105,  206 => 104,  205 => 103,  204 => 102,  203 => 101,  202 => 100,  201 => 99,  200 => 98,  199 => 97,  198 => 96,  197 => 95,  194 => 94,  191 => 93,  188 => 92,  185 => 91,  183 => 90,  181 => 88,  179 => 86,  176 => 85,  169 => 84,  164 => 109,  149 => 108,  146 => 84,  128 => 83,  121 => 82,  116 => 80,  108 => 77,  101 => 75,  99 => 74,  96 => 73,  89 => 72,  84 => 82,  81 => 81,  78 => 72,  71 => 71,  65 => 60,  63 => 69,  61 => 67,  59 => 66,  57 => 65,  55 => 64,  53 => 63,  46 => 60,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "field--link.html.twig", "themes/custom/bsi_bund/templates/field/field--link.html.twig");
    }
    
    public function ensureSecurityChecked(): void
    {
        if ($this->sandbox->isSandboxed($this->source)) {
            $this->checkSecurity();
        }
    }
    
    public function checkSecurity()
    {
        static $tags = ["extends" => 60, "set" => 63, "block" => 72, "if" => 73, "for" => 83, "include" => 95];
        static $filters = ["default" => 63, "escape" => 75, "map" => 101];
        static $functions = ["create_attribute" => 100];
        static $tests = [];

        try {
            $this->sandbox->checkSecurity(
                [0 => "extends", 1 => "set", 2 => "block", 3 => "if", 4 => "for", 5 => "include"],
                [0 => "default", 1 => "escape", 2 => "map"],
                [0 => "create_attribute"],
                [],
                $this->source
            );
        } catch (SecurityError $e) {
            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            } elseif ($e instanceof SecurityNotAllowedTestError && isset($tests[$e->getTestName()])) {
                $e->setTemplateLine($tests[$e->getTestName()]);
            }

            throw $e;
        }

    }
}
