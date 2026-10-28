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

/* field.html.twig */
class __TwigTemplate_cc0aee6e6f2262f384e1a5aa69003aef extends Template
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
            'content' => [$this, 'block_content'],
            'label' => [$this, 'block_label'],
            'items' => [$this, 'block_items'],
            'item' => [$this, 'block_item'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 40
        $context["fully_inline"] = ( !(($tmp = ($context["multiple"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp) && (($context["label_display"] ?? null) == "inline"));
        // line 41
        $context["base_name"] = ((array_key_exists("base_name", $context)) ? (Twig\Extension\CoreExtension::default(($context["base_name"] ?? null), "field")) : ("field"));
        // line 42
        $context["field_wrapper"] = true;
        // line 43
        yield "
";
        // line 45
        $context["classes"] = [        // line 46
($context["base_name"] ?? null), ((false) ? (("field--name-" . \Drupal\Component\Utility\Html::getClass(        // line 47
($context["field_name"] ?? null)))) : ("")), ((false) ? (("field--type-" . \Drupal\Component\Utility\Html::getClass(        // line 48
($context["field_type"] ?? null)))) : ("")), ((false) ? (("field--label-" .         // line 49
($context["label_display"] ?? null))) : ("")), (((        // line 50
($context["base_name"] ?? null) == "field")) ? (("field--label-" . ($context["label_display"] ?? null))) : ("")), ((((        // line 51
($context["label_display"] ?? null) == "inline") &&  !(($tmp = ($context["fully_inline"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp))) ? ("clearfix") : (""))];
        // line 53
        $context["title_classes"] = [(        // line 54
($context["base_name"] ?? null) . "__label"), (((        // line 55
($context["label_display"] ?? null) == "visually_hidden")) ? ("visually-hidden") : (""))];
        // line 57
        yield from $this->unwrap()->yieldBlock('content', $context, $blocks);
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["multiple", "label_display", "field_name", "field_type", "attributes", "label_hidden", "title_attributes", "label", "items"]);        return; yield;
    }

    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 58
        yield "    ";
        if ((($tmp = ($context["field_wrapper"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 59
            yield "<div";
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["attributes"] ?? null), "addClass", [($context["classes"] ?? null)], "method", false, false, true, 59), "html", null, true);
            yield ">
    ";
        }
        // line 61
        yield "            ";
        yield from $this->unwrap()->yieldBlock('label', $context, $blocks);
        // line 71
        yield "
            ";
        // line 72
        yield from $this->unwrap()->yieldBlock('items', $context, $blocks);
        // line 91
        yield "    ";
        if ((($tmp = ($context["field_wrapper"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 92
            yield "</div>
    ";
        }
        return; yield;
    }

    // line 61
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_label(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 62
        yield "            ";
        if ( !(($tmp = ($context["label_hidden"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 63
            if ((($tmp = ($context["fully_inline"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 65
                yield "                    <span";
                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["title_attributes"] ?? null), "addClass", [($context["title_classes"] ?? null)], "method", false, false, true, 65), "html", null, true);
                yield ">";
                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["label"] ?? null), "html", null, true);
                yield ":</span>";
            } else {
                // line 67
                yield "<div";
                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["title_attributes"] ?? null), "addClass", [($context["title_classes"] ?? null)], "method", false, false, true, 67), "html", null, true);
                yield ">";
                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["label"] ?? null), "html", null, true);
                yield "</div>";
            }
        }
        // line 70
        yield "            ";
        return; yield;
    }

    // line 72
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_items(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 73
        yield "            ";
        if (( !(($tmp = ($context["label_hidden"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp) && (($tmp = ($context["multiple"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp))) {
            // line 74
            yield "            <div class=\"";
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["base_name"] ?? null), "html", null, true);
            yield "__items\">
                ";
        }
        // line 76
        yield "                ";
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
            // line 77
            yield "                    ";
            yield from $this->unwrap()->yieldBlock('item', $context, $blocks);
            // line 86
            yield "                ";
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
        // line 87
        yield "                ";
        if (( !(($tmp = ($context["label_hidden"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp) && (($tmp = ($context["multiple"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp))) {
            // line 88
            yield "            </div>
            ";
        }
        // line 90
        yield "            ";
        return; yield;
    }

    // line 77
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_item(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 78
        yield "                    ";
        if ((($tmp = ($context["fully_inline"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 79
            yield "<span";
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["item"] ?? null), "attributes", [], "any", false, false, true, 79), "addClass", [(($context["base_name"] ?? null) . "__item")], "method", false, false, true, 79), "html", null, true);
            yield ">";
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["item"] ?? null), "content", [], "any", false, false, true, 79), "html", null, true);
            yield "</span>";
        } else {
            // line 81
            yield "<div";
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["item"] ?? null), "attributes", [], "any", false, false, true, 81), "addClass", [(($context["base_name"] ?? null) . "__item")], "method", false, false, true, 81), "html", null, true);
            yield ">
                        ";
            // line 82
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["item"] ?? null), "content", [], "any", false, false, true, 82), "html", null, true);
            yield "
                    </div>";
        }
        // line 85
        yield "                    ";
        return; yield;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "field.html.twig";
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
        return array (  232 => 85,  227 => 82,  222 => 81,  215 => 79,  212 => 78,  205 => 77,  200 => 90,  196 => 88,  193 => 87,  178 => 86,  175 => 77,  157 => 76,  151 => 74,  148 => 73,  141 => 72,  136 => 70,  128 => 67,  121 => 65,  119 => 63,  116 => 62,  109 => 61,  102 => 92,  99 => 91,  97 => 72,  94 => 71,  91 => 61,  85 => 59,  82 => 58,  70 => 57,  68 => 55,  67 => 54,  66 => 53,  64 => 51,  63 => 50,  62 => 49,  61 => 48,  60 => 47,  59 => 46,  58 => 45,  55 => 43,  53 => 42,  51 => 41,  49 => 40,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "field.html.twig", "themes/custom/bsi_bund/templates/field/field.html.twig");
    }
    
    public function ensureSecurityChecked(): void
    {
        if ($this->sandbox->isSandboxed($this->source)) {
            $this->checkSecurity();
        }
    }
    
    public function checkSecurity()
    {
        static $tags = ["set" => 40, "block" => 57, "if" => 58, "for" => 76];
        static $filters = ["default" => 41, "clean_class" => 47, "escape" => 59];
        static $functions = [];
        static $tests = [];

        try {
            $this->sandbox->checkSecurity(
                [0 => "set", 1 => "block", 2 => "if", 3 => "for"],
                [0 => "default", 1 => "clean_class", 2 => "escape"],
                [],
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
