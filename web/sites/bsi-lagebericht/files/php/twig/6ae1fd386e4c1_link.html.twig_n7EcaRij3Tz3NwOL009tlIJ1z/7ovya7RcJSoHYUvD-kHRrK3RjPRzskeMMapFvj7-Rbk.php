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

/* themes/custom/bsi_bund/templates/elements/link.html.twig */
class __TwigTemplate_28cb105d5bcb91a62623d2e103037f1d extends Template
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

        $this->parent = false;

        $this->blocks = [
            'icon' => [$this, 'block_icon'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 9
        $context["base_class"] = ((array_key_exists("base_class", $context)) ? (Twig\Extension\CoreExtension::default(($context["base_class"] ?? null), "bsi-link")) : ("bsi-link"));
        // line 10
        $context["icon_position"] = ((array_key_exists("icon_position", $context)) ? (Twig\Extension\CoreExtension::default(($context["icon_position"] ?? null), "before")) : ("before"));
        // line 11
        $context["icon"] = (((array_key_exists("icon", $context) &&  !(null === ($context["icon"] ?? null)))) ? (($context["icon"] ?? null)) : ((($_v0 = ["internal" => "chevron-right", "external" => "external-link", "download" => "download", "" => false]) && is_array($_v0) || $_v0 instanceof ArrayAccess && in_array($_v0::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v0[(($_v1 = ((        // line 16
array_key_exists("link_type", $context)) ? (Twig\Extension\CoreExtension::default(($context["link_type"] ?? null), "")) : (""))) instanceof \Stringable && (is_array($_v0) || $_v0 instanceof \ArrayObject || $_v0 instanceof \ArrayIterator) ? (string) $_v1 : $_v1)] ?? null) : CoreExtension::getAttribute($this->env, $this->source, ["internal" => "chevron-right", "external" => "external-link", "download" => "download", "" => false], ((array_key_exists("link_type", $context)) ? (Twig\Extension\CoreExtension::default(($context["link_type"] ?? null), "")) : ("")), [], "array", false, false, true, 12))));
        // line 17
        yield "
";
        // line 18
        if ((($context["link_type"] ?? null) == "external")) {
            // line 19
            yield "  ";
            $context["target"] = "_blank";
        }
        // line 21
        yield "
";
        // line 22
        $context["link_title"] = Twig\Extension\CoreExtension::default(((array_key_exists("title_attr", $context)) ? (Twig\Extension\CoreExtension::default(($context["title_attr"] ?? null), CoreExtension::getAttribute($this->env, $this->source, ($context["attributes"] ?? null), "title", [], "any", false, false, true, 22))) : (CoreExtension::getAttribute($this->env, $this->source, ($context["attributes"] ?? null), "title", [], "any", false, false, true, 22))), null);
        // line 23
        yield "
";
        // line 24
        $context["attributes"] = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ((array_key_exists("attributes", $context)) ? (Twig\Extension\CoreExtension::default(($context["attributes"] ?? null), $this->extensions['Drupal\Core\Template\TwigExtension']->createAttribute())) : ($this->extensions['Drupal\Core\Template\TwigExtension']->createAttribute())), "addClass", [        // line 25
($context["base_class"] ?? null), ($context["extra_classes"] ?? null)], "method", false, false, true, 24), "setAttribute", [(((($tmp =         // line 26
($context["target"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("target") : ("")), ($context["target"] ?? null)], "method", false, false, true, 25), "setAttribute", [(((($tmp =         // line 27
($context["link_title"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("title") : ("")), $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(($context["link_title"] ?? null))], "method", false, false, true, 26), "setAttribute", [(((        // line 28
($context["link_type"] ?? null) == "button")) ? ("type") : ("")), ($context["link_type"] ?? null)], "method", false, false, true, 27);
        // line 30
        yield "
";
        // line 31
        if ((($tmp = ($context["modifiers"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 32
            yield "  ";
            // line 33
            yield "  ";
            $context["attributes"] = CoreExtension::getAttribute($this->env, $this->source, ($context["attributes"] ?? null), "addClass", [Twig\Extension\CoreExtension::map($this->env, $this->env->hasExtension(\Twig\Extension\SandboxExtension::class) && $this->env->getExtension(\Twig\Extension\SandboxExtension::class)->getChecker()->isSandboxed($this->source),             // line 34
($context["modifiers"] ?? null), function ($__m__) use ($context, $macros) { $context["m"] = $__m__; return ((($context["base_class"] ?? null) . "-") . ($context["m"] ?? null)); })], "method", false, false, true, 33);
        }
        // line 37
        yield "
";
        // line 38
        if (CoreExtension::inFilter(($context["link_type"] ?? null), ["span", "button"])) {
            // line 39
            yield "  <";
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["link_type"] ?? null), "html", null, true);
            yield " ";
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["attributes"] ?? null), "html", null, true);
            yield ">
";
        } else {
            // line 41
            yield "  <a href=\"";
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["url"] ?? null), "html", null, true);
            yield "\" ";
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["attributes"] ?? null), "html", null, true);
            yield ">
";
        }
        // line 43
        yield "
  ";
        // line 45
        yield "  ";
        if (((($tmp = ($context["icon"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp) && (($context["icon_position"] ?? null) == "before"))) {
            // line 46
            yield "    ";
            yield from $this->unwrap()->yieldBlock('icon', $context, $blocks);
            // line 53
            yield "  ";
        }
        // line 54
        yield "
  ";
        // line 55
        if ((((($tmp = ($context["title"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp) || (($tmp = ($context["add_on"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) || ((($tmp = ($context["icon"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp) && (($context["icon_position"] ?? null) == "after")))) {
            // line 56
            yield "    <span class=\"";
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["base_class"] ?? null), "html", null, true);
            yield "__content\">
      ";
            // line 58
            yield "      ";
            if ((($tmp = ($context["title"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 59
                yield "        <span class=\"";
                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["base_class"] ?? null), "html", null, true);
                yield "__text\">";
                // line 60
                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["title"] ?? null), "html", null, true);
                // line 61
                yield "</span>
        ";
                // line 62
                if ((($tmp = ($context["file_size"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    // line 63
                    yield "          ";
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["file_size"] ?? null), "html", null, true);
                    yield "
        ";
                }
                // line 65
                yield "        ";
                if ((($tmp = ($context["file_extension"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    // line 66
                    yield "          ";
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["file_extension"] ?? null), "html", null, true);
                    yield "
        ";
                }
                // line 68
                yield "      ";
            }
            // line 69
            yield "
      ";
            // line 71
            yield "      ";
            if ((($tmp = ($context["add_on"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 72
                yield "        <span class=\"";
                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["base_class"] ?? null), "html", null, true);
                yield "__add-on\">";
                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["add_on"] ?? null), "html", null, true);
                yield "</span>
      ";
            }
            // line 74
            yield "
      ";
            // line 76
            yield "      ";
            if (((($tmp = ($context["icon"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp) && (($context["icon_position"] ?? null) == "after"))) {
                // line 77
                yield "        ";
                yield from                 $this->unwrap()->yieldBlock("icon", $context, $blocks);
                yield "
      ";
            }
            // line 79
            yield "    </span>
  ";
        }
        // line 81
        if (!CoreExtension::inFilter(($context["link_type"] ?? null), ["span", "button"])) {
            // line 82
            yield "</a>
";
        } else {
            // line 84
            yield "</";
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["link_type"] ?? null), "html", null, true);
            yield ">
";
        }
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["link_type", "title_attr", "attributes", "extra_classes", "modifiers", "m", "url", "title", "add_on", "file_size", "file_extension"]);        return; yield;
    }

    // line 46
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_icon(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 47
        yield from $this->load("@bsi_bund/components/icon.twig", 47)->unwrap()->yield(CoreExtension::toArray(["name" =>         // line 48
($context["icon"] ?? null), "decorative" => true, "extra_classes" => (        // line 50
($context["base_class"] ?? null) . "__icon")]));
        return; yield;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "themes/custom/bsi_bund/templates/elements/link.html.twig";
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
        return array (  218 => 50,  217 => 48,  216 => 47,  209 => 46,  199 => 84,  195 => 82,  193 => 81,  189 => 79,  183 => 77,  180 => 76,  177 => 74,  169 => 72,  166 => 71,  163 => 69,  160 => 68,  154 => 66,  151 => 65,  145 => 63,  143 => 62,  140 => 61,  138 => 60,  134 => 59,  131 => 58,  126 => 56,  124 => 55,  121 => 54,  118 => 53,  115 => 46,  112 => 45,  109 => 43,  101 => 41,  93 => 39,  91 => 38,  88 => 37,  85 => 34,  83 => 33,  81 => 32,  79 => 31,  76 => 30,  74 => 28,  73 => 27,  72 => 26,  71 => 25,  70 => 24,  67 => 23,  65 => 22,  62 => 21,  58 => 19,  56 => 18,  53 => 17,  51 => 16,  50 => 11,  48 => 10,  46 => 9,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "themes/custom/bsi_bund/templates/elements/link.html.twig", "/var/www/html/web/themes/custom/bsi_bund/templates/elements/link.html.twig");
    }
    
    public function ensureSecurityChecked(): void
    {
        if ($this->sandbox->isSandboxed($this->source)) {
            $this->checkSecurity();
        }
    }
    
    public function checkSecurity()
    {
        static $tags = ["set" => 9, "if" => 18, "block" => 46, "include" => 47];
        static $filters = ["default" => 9, "render" => 27, "map" => 34, "escape" => 39];
        static $functions = ["create_attribute" => 24, "block" => 77];
        static $tests = [];

        try {
            $this->sandbox->checkSecurity(
                [0 => "set", 1 => "if", 2 => "block", 3 => "include"],
                [0 => "default", 1 => "render", 2 => "map", 3 => "escape"],
                [0 => "create_attribute", 1 => "block"],
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
