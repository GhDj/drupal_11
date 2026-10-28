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

/* themes/custom/bsi_bund/templates/menu/menu--header.html.twig */
class __TwigTemplate_e47cb052e854a4a9480ed22b4e1b4dc4 extends Template
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
        // line 2
        yield "
";
        // line 3
        $macros["menus"] = $this->macros["menus"] = $this->getMacroNamespace();
        // line 4
        yield "
";
        // line 5
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(($macros["menus"] ?? $this->throwUninitializedMacroNamespace(5))->call("menu_links", [($context["items"] ?? null), ($context["attributes"] ?? null), 0], $context, 5, $this->source));
        yield "

";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["_self", "items", "attributes"]);        return; yield;
    }

    protected function loadDeclaredMacros(): array
    {
        return [
            "menu_links" => new \Twig\TwigMacro("menu_links", function ($items = null, $attributes = null, $menu_level = null, ...$varargs): string|Markup {
                // line 7
                $macros = $this->macros;
                $context = [
                    "items" => $items,
                    "attributes" => $attributes,
                    "menu_level" => $menu_level,
                    "varargs" => $varargs,
                ] + $this->env->getGlobals();

                $blocks = [];

                return ('' === $tmp = implode('', iterator_to_array((function () use (&$context, $macros, $blocks) {
                    // line 8
                    yield "  ";
                    if ((($tmp = ($context["items"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                        // line 9
                        yield "    <ul class=\"bsi-metanav__list\">
      ";
                        // line 10
                        $context['_parent'] = $context;
                        $context['_seq'] = CoreExtension::ensureTraversable(($context["items"] ?? null));
                        foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
                            // line 11
                            yield "        <li class=\"bsi-metanav__item\">
          ";
                            // line 12
                            if ((($tmp = ($context["icon"] = ((CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["item"], "original_link", [], "any", false, true, true, 12), "entity", [], "any", false, true, true, 12), "field_icon", [], "any", false, true, true, 12), "target_id", [], "any", true, true, true, 12)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["item"], "original_link", [], "any", false, false, true, 12), "entity", [], "any", false, false, true, 12), "field_icon", [], "any", false, false, true, 12), "target_id", [], "any", false, false, true, 12), false)) : (false)))) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                                // line 13
                                yield "            ";
                                $context["icon"] = (($_v0 = Twig\Extension\CoreExtension::split($this->env->getCharset(), ($context["icon"] ?? null), ":")) && is_array($_v0) || $_v0 instanceof ArrayAccess && in_array($_v0::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v0[1] ?? null) : CoreExtension::getAttribute($this->env, $this->source, Twig\Extension\CoreExtension::split($this->env->getCharset(), ($context["icon"] ?? null), ":"), 1, [], "array", false, false, true, 13));
                                // line 14
                                yield "          ";
                            } elseif ((is_string($_v1 = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["item"], "original_link", [], "any", false, false, true, 14), "pluginId", [], "any", false, false, true, 14)) && is_string($_v2 = "header.language:") && str_starts_with($_v1, $_v2))) {
                                // line 15
                                yield "            ";
                                $context["icon"] = "globe";
                                // line 16
                                yield "          ";
                            }
                            // line 17
                            yield "
          ";
                            // line 18
                            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["item"], "url", [], "any", false, false, true, 18), "isExternal", [], "method", false, false, true, 18)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                                // line 19
                                yield "            ";
                                $context["target"] = "_blank";
                                // line 20
                                yield "          ";
                            } else {
                                // line 21
                                yield "            ";
                                $context["target"] = "";
                                // line 22
                                yield "          ";
                            }
                            // line 23
                            yield "
          ";
                            // line 24
                            yield from $this->load("@bsi_bund/elements/link.html.twig", 24)->unwrap()->yield(CoreExtension::toArray(["url" => CoreExtension::getAttribute($this->env, $this->source,                             // line 25
$context["item"], "url", [], "any", false, false, true, 25), "title" => CoreExtension::getAttribute($this->env, $this->source,                             // line 26
$context["item"], "title", [], "any", false, false, true, 26), "extra_classes" => "bsi-metanav__link", "target" => (((                            // line 28
array_key_exists("target", $context) &&  !(null === $context["target"]))) ? ($context["target"]) : (null)), "icon" =>                             // line 29
($context["icon"] ?? null), "icon_position" => "after"]));
                            // line 32
                            yield "        </li>
      ";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_key'], $context['item'], $context['_parent']);
                        $context = array_intersect_key($context, $_parent);
                        $context += $_parent;
                        // line 34
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
        return "themes/custom/bsi_bund/templates/menu/menu--header.html.twig";
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
        return array (  139 => 34,  131 => 32,  129 => 29,  128 => 28,  127 => 26,  126 => 25,  125 => 24,  122 => 23,  119 => 22,  116 => 21,  113 => 20,  110 => 19,  108 => 18,  105 => 17,  102 => 16,  99 => 15,  96 => 14,  93 => 13,  91 => 12,  88 => 11,  84 => 10,  81 => 9,  78 => 8,  66 => 7,  53 => 5,  50 => 4,  48 => 3,  45 => 2,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "themes/custom/bsi_bund/templates/menu/menu--header.html.twig", "/var/www/html/web/themes/custom/bsi_bund/templates/menu/menu--header.html.twig");
    }
    
    public function ensureSecurityChecked(): void
    {
        if ($this->sandbox->isSandboxed($this->source)) {
            $this->checkSecurity();
        }
    }
    
    public function checkSecurity()
    {
        static $tags = ["import" => 3, "macro" => 7, "if" => 8, "for" => 10, "set" => 13, "include" => 24];
        static $filters = ["default" => 12, "split" => 13];
        static $functions = [];
        static $tests = [];

        try {
            $this->sandbox->checkSecurity(
                [0 => "import", 1 => "macro", 2 => "if", 3 => "for", 4 => "set", 5 => "include"],
                [0 => "default", 1 => "split"],
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
