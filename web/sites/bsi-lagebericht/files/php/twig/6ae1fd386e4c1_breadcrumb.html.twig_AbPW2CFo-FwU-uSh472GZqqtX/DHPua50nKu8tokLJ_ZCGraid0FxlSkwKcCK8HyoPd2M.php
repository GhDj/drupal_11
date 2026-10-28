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

/* themes/custom/bsi_bund/templates/breadcrumb.html.twig */
class __TwigTemplate_b2f0ff8f67962410893d97d803cccce2 extends Template
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
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 10
        if ((($tmp = ($context["breadcrumb"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 11
            yield "  <nav class=\"bsi-breadcrumb\" aria-labelledby=\"system-breadcrumb\">
    <span id=\"system-breadcrumb\" class=\"visually-hidden\">";
            // line 12
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Breadcrumb"));
            yield "</span>
    <ol class=\"bsi-breadcrumb__list\">
      ";
            // line 14
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(($context["breadcrumb"] ?? null));
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
                // line 15
                yield "
        ";
                // line 17
                yield "        ";
                if (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["loop"], "last", [], "any", false, false, true, 17)) && $tmp instanceof Markup ? (string) $tmp : $tmp) &&  !(($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["loop"], "first", [], "any", false, false, true, 17)) && $tmp instanceof Markup ? (string) $tmp : $tmp))) {
                    // line 18
                    yield "          <li class=\"bsi-breadcrumb__list-item\" aria-current=\"page\">
              ";
                    // line 19
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["item"], "text", [], "any", false, false, true, 19), "html", null, true);
                    yield "
          </li>

        ";
                } else {
                    // line 23
                    yield "
          <li class=\"bsi-breadcrumb__list-item\">
            ";
                    // line 26
                    yield "            ";
                    if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["loop"], "first", [], "any", false, false, true, 26)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                        // line 27
                        yield "              ";
                        yield from $this->load("@bsi_bund/elements/link.html.twig", 27)->unwrap()->yield(CoreExtension::toArray(["url" => CoreExtension::getAttribute($this->env, $this->source,                         // line 28
$context["item"], "url", [], "any", false, false, true, 28), "attributes" => CoreExtension::getAttribute($this->env, $this->source, $this->extensions['Drupal\Core\Template\TwigExtension']->createAttribute(), "addClass", [["bsi-link", "bsi-breadcrumb__home"]], "method", false, false, true, 29), "link_type" => "internal", "icon" => "home", "icon_position" => "before"]));
                        // line 37
                        yield "            ";
                    } else {
                        // line 38
                        yield "              ";
                        // line 39
                        yield "              ";
                        $context["url"] = CoreExtension::getAttribute($this->env, $this->source, (($_v0 = ($context["links"] ?? null)) && is_array($_v0) || $_v0 instanceof ArrayAccess && in_array($_v0::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v0[(($_v1 = CoreExtension::getAttribute($this->env, $this->source, $context["loop"], "index0", [], "any", false, false, true, 39)) instanceof \Stringable && (is_array($_v0) || $_v0 instanceof \ArrayObject || $_v0 instanceof \ArrayIterator) ? (string) $_v1 : $_v1)] ?? null) : CoreExtension::getAttribute($this->env, $this->source, ($context["links"] ?? null), CoreExtension::getAttribute($this->env, $this->source, $context["loop"], "index0", [], "any", false, false, true, 39), [], "array", false, false, true, 39)), "url", [], "any", false, false, true, 39);
                        // line 40
                        yield "              ";
                        if (((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["url"] ?? null), "isRouted", [], "method", false, false, true, 40)) && $tmp instanceof Markup ? (string) $tmp : $tmp) && (CoreExtension::getAttribute($this->env, $this->source, ($context["url"] ?? null), "getRouteName", [], "method", false, false, true, 40) == "<nolink>"))) {
                            // line 41
                            yield "                <span class=\"bsi-link bsi-breadcrumb__link\">";
                            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["item"], "text", [], "any", false, false, true, 41), "html", null, true);
                            yield "</span>
              ";
                        } else {
                            // line 43
                            yield "                ";
                            yield from $this->load("@bsi_bund/elements/link.html.twig", 43)->unwrap()->yield(CoreExtension::toArray(["url" => CoreExtension::getAttribute($this->env, $this->source,                             // line 44
$context["item"], "url", [], "any", false, false, true, 44), "title" => CoreExtension::getAttribute($this->env, $this->source,                             // line 45
$context["item"], "text", [], "any", false, false, true, 45), "icon" => "", "attributes" => CoreExtension::getAttribute($this->env, $this->source, $this->extensions['Drupal\Core\Template\TwigExtension']->createAttribute(), "addClass", [["bsi-link", "bsi-breadcrumb__link"]], "method", false, false, true, 47), "link_type" => "internal"]));
                            // line 53
                            yield "              ";
                        }
                        // line 54
                        yield "            ";
                    }
                    // line 55
                    yield "          </li>

        ";
                }
                // line 58
                yield "      ";
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
            // line 59
            yield "    </ol>
  </nav>
";
        }
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["breadcrumb", "loop", "links"]);        return; yield;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "themes/custom/bsi_bund/templates/breadcrumb.html.twig";
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
        return array (  147 => 59,  132 => 58,  127 => 55,  124 => 54,  121 => 53,  119 => 45,  118 => 44,  116 => 43,  110 => 41,  107 => 40,  104 => 39,  102 => 38,  99 => 37,  97 => 28,  95 => 27,  92 => 26,  88 => 23,  81 => 19,  78 => 18,  75 => 17,  72 => 15,  55 => 14,  50 => 12,  47 => 11,  45 => 10,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "themes/custom/bsi_bund/templates/breadcrumb.html.twig", "/var/www/html/web/themes/custom/bsi_bund/templates/breadcrumb.html.twig");
    }
    
    public function ensureSecurityChecked(): void
    {
        if ($this->sandbox->isSandboxed($this->source)) {
            $this->checkSecurity();
        }
    }
    
    public function checkSecurity()
    {
        static $tags = ["if" => 10, "for" => 14, "include" => 27, "set" => 39];
        static $filters = ["t" => 12, "escape" => 19];
        static $functions = ["create_attribute" => 29];
        static $tests = [];

        try {
            $this->sandbox->checkSecurity(
                [0 => "if", 1 => "for", 2 => "include", 3 => "set"],
                [0 => "t", 1 => "escape"],
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
