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

/* themes/custom/bsi_bund/templates/menu/menu--social-media.html.twig */
class __TwigTemplate_9ac32969c23ff3397ad1790846fdd947 extends Template
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
        // line 44
        $macros["menus"] = $this->macros["menus"] = $this->getMacroNamespace();
        // line 45
        yield "
";
        // line 50
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(($macros["menus"] ?? $this->throwUninitializedMacroNamespace(50))->call("menu_links", [($context["items"] ?? null), ($context["attributes"] ?? null), 0], $context, 50, $this->source));
        yield "

";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["_self", "items", "attributes"]);        return; yield;
    }

    protected function loadDeclaredMacros(): array
    {
        return [
            "menu_links" => new \Twig\TwigMacro("menu_links", function ($items = null, $attributes = null, $menu_level = null, ...$varargs): string|Markup {
                // line 52
                $macros = $this->macros;
                $context = [
                    "items" => $items,
                    "attributes" => $attributes,
                    "menu_level" => $menu_level,
                    "varargs" => $varargs,
                ] + $this->env->getGlobals();

                $blocks = [];

                return ('' === $tmp = implode('', iterator_to_array((function () use (&$context, $macros, $blocks) {
                    // line 53
                    yield "  ";
                    $macros["menus"] = $this->getMacroNamespace();
                    // line 54
                    yield "  ";
                    if ((($tmp = ($context["items"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                        // line 55
                        yield "    <ul class=\"bsi-footer__social-links\">
    ";
                        // line 56
                        $context['_parent'] = $context;
                        $context['_seq'] = CoreExtension::ensureTraversable(($context["items"] ?? null));
                        foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
                            // line 57
                            yield "      <li";
                            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["item"], "attributes", [], "any", false, false, true, 57), "html", null, true);
                            yield ">

          ";
                            // line 59
                            yield from $this->load("@bsi_bund/elements/link.html.twig", 59)->unwrap()->yield(CoreExtension::toArray(["url" => CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source,                             // line 60
$context["item"], "url", [], "any", false, false, true, 60), "toString", [], "any", false, false, true, 60), "title" => CoreExtension::getAttribute($this->env, $this->source,                             // line 61
$context["item"], "title", [], "any", false, false, true, 61), "extra_classes" => "bsi-footer__social-link", "link_type" => (((($tmp = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source,                             // line 63
$context["item"], "url", [], "any", false, false, true, 63), "isExternal", [], "any", false, false, true, 63)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("external") : ("internal")), "target" => (((($tmp = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source,                             // line 64
$context["item"], "url", [], "any", false, false, true, 64), "isExternal", [], "any", false, false, true, 64)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("_blank") : ("_self"))]));
                            // line 66
                            yield "

      </li>
    ";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_key'], $context['item'], $context['_parent']);
                        $context = array_intersect_key($context, $_parent);
                        $context += $_parent;
                        // line 70
                        yield "    </ul>
  ";
                    }
                    return; yield;
                })(), false))) ? '' : new Markup($tmp, $this->env->getCharset());
            }, ["items" => false, "attributes" => false, "menu_level" => false], false),
        ];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "themes/custom/bsi_bund/templates/menu/menu--social-media.html.twig";
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
        return array (  110 => 70,  100 => 66,  98 => 64,  97 => 63,  96 => 61,  95 => 60,  94 => 59,  88 => 57,  84 => 56,  81 => 55,  78 => 54,  75 => 53,  63 => 52,  50 => 50,  47 => 45,  45 => 44,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "themes/custom/bsi_bund/templates/menu/menu--social-media.html.twig", "/var/www/html/web/themes/custom/bsi_bund/templates/menu/menu--social-media.html.twig");
    }
    
    public function ensureSecurityChecked(): void
    {
        if ($this->sandbox->isSandboxed($this->source)) {
            $this->checkSecurity();
        }
    }
    
    public function checkSecurity()
    {
        static $tags = ["import" => 44, "macro" => 52, "if" => 54, "for" => 56, "include" => 59];
        static $filters = ["escape" => 57];
        static $functions = [];
        static $tests = [];

        try {
            $this->sandbox->checkSecurity(
                [0 => "import", 1 => "macro", 2 => "if", 3 => "for", 4 => "include"],
                [0 => "escape"],
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
