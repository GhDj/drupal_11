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

/* themes/custom/bsi_bund/templates/menu/menu--main--header.html.twig */
class __TwigTemplate_42fc4ce2964bf73cd99915becf9aae01 extends Template
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
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(($macros["menus"] ?? $this->throwUninitializedMacroNamespace(5))->call("level1", [($context["items"] ?? null)], $context, 5, $this->source));
        yield "

";
        // line 36
        yield "
";
        // line 86
        yield "
";
        // line 114
        yield "
";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["_self", "items", "loop", "parent", "l2Index"]);        return; yield;
    }

    protected function loadDeclaredMacros(): array
    {
        return [
            "level1" => new \Twig\TwigMacro("level1", function ($items = null, ...$varargs): string|Markup {
                // line 7
                $macros = $this->macros;
                $context = [
                    "items" => $items,
                    "varargs" => $varargs,
                ] + $this->env->getGlobals();

                $blocks = [];

                return ('' === $tmp = implode('', iterator_to_array((function () use (&$context, $macros, $blocks) {
                    // line 8
                    yield "  ";
                    $macros["menus"] = $this->getMacroNamespace();
                    // line 9
                    yield "  ";
                    if ((($tmp = ($context["items"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                        // line 10
                        yield "    <ul class=\"bsi-mainmenu__list\">
      ";
                        // line 11
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
                            // line 12
                            yield "        ";
                            $context["l1Index"] = CoreExtension::getAttribute($this->env, $this->source, $context["loop"], "index0", [], "any", false, false, true, 12);
                            // line 13
                            yield "        <li class=\"bsi-mainmenu__list-item\">
          ";
                            // line 14
                            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["item"], "below", [], "any", false, false, true, 14)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                                // line 15
                                yield "            ";
                                $context["panelId"] = ("mainMenuL2-" . ($context["l1Index"] ?? null));
                                // line 16
                                yield "            <button type=\"button\"
                    class=\"bsi-button bsi-button--primary bsi-mainmenu__collapse-toggle\"
                    aria-controls=\"";
                                // line 18
                                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["panelId"] ?? null), "html", null, true);
                                yield "\"
                    aria-expanded=\"false\"
                    data-js-mainmenu-toggle=\"l1\">
              <span class=\"bsi-button__text\">";
                                // line 21
                                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["item"], "title", [], "any", false, false, true, 21), "html", null, true);
                                yield "</span>
            </button>
            ";
                                // line 23
                                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(($macros["menus"] ?? $this->throwUninitializedMacroNamespace(23))->call("l2_panel", [$context["item"], CoreExtension::getAttribute($this->env, $this->source, $context["item"], "below", [], "any", false, false, true, 23), ($context["panelId"] ?? null), ($context["l1Index"] ?? null)], $context, 23, $this->source));
                                yield "
          ";
                            } else {
                                // line 25
                                yield "            ";
                                yield from $this->load("@bsi_bund/elements/link.html.twig", 25)->unwrap()->yield(CoreExtension::toArray(["url" => CoreExtension::getAttribute($this->env, $this->source,                                 // line 26
$context["item"], "url", [], "any", false, false, true, 26), "title" => CoreExtension::getAttribute($this->env, $this->source,                                 // line 27
$context["item"], "title", [], "any", false, false, true, 27), "extra_classes" => "bsi-mainmenu__link"]));
                                // line 30
                                yield "          ";
                            }
                            // line 31
                            yield "        </li>
      ";
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
                        // line 33
                        yield "    </ul>
  ";
                    }
                    return; yield;
                })(), false))) ? '' : new Markup($tmp, $this->env->getCharset());
            }, ["items" => false], false),
            "l2_panel" => new \Twig\TwigMacro("l2_panel", function ($parent = null, $items = null, $panelId = null, $l1Index = null, ...$varargs): string|Markup {
                // line 37
                $macros = $this->macros;
                $context = [
                    "parent" => $parent,
                    "items" => $items,
                    "panelId" => $panelId,
                    "l1Index" => $l1Index,
                    "varargs" => $varargs,
                ] + $this->env->getGlobals();

                $blocks = [];

                return ('' === $tmp = implode('', iterator_to_array((function () use (&$context, $macros, $blocks) {
                    // line 38
                    yield "  ";
                    $macros["menus"] = $this->getMacroNamespace();
                    // line 39
                    yield "  <div class=\"bsi-mainmenu__panel bsi-mainmenu__panel--l2\"
       id=\"";
                    // line 40
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["panelId"] ?? null), "html", null, true);
                    yield "\"
       data-js-mainmenu-panel=\"l2\"
       hidden>
    <button type=\"button\"
            class=\"bsi-button bsi-button--inline-action bsi-mainmenu__back\"
            data-js-mainmenu-back=\"\"
            aria-label=\"";
                    // line 46
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Back to main menu"));
                    yield "\">
      ";
                    // line 47
                    yield from $this->load("@bsi_bund/components/icon.twig", 47)->unwrap()->yield(CoreExtension::toArray(["name" => "chevron-left", "decorative" => true, "extra_classes" => "bsi-button__icon"]));
                    // line 52
                    yield "      <span class=\"bsi-button__text\">";
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["parent"] ?? null), "title", [], "any", false, false, true, 52), "html", null, true);
                    yield "</span>
    </button>

    ";
                    // line 57
                    yield "    ";
                    yield from $this->load("@bsi_bund/elements/link.html.twig", 57)->unwrap()->yield(CoreExtension::toArray(["url" => CoreExtension::getAttribute($this->env, $this->source,                     // line 58
($context["parent"] ?? null), "url", [], "any", false, false, true, 58), "title" => t("Overview: @title", ["@title" => CoreExtension::getAttribute($this->env, $this->source,                     // line 59
($context["parent"] ?? null), "title", [], "any", false, false, true, 59)]), "extra_classes" => "bsi-mainmenu__overview-link bsi-mainmenu__overview-link--mobile"]));
                    // line 62
                    yield "
    <div class=\"bsi-mainmenu__overview-column\">
      ";
                    // line 64
                    yield from $this->load("@bsi_bund/elements/link.html.twig", 64)->unwrap()->yield(CoreExtension::toArray(["url" => CoreExtension::getAttribute($this->env, $this->source,                     // line 65
($context["parent"] ?? null), "url", [], "any", false, false, true, 65), "title" => t("Overview: @title", ["@title" => CoreExtension::getAttribute($this->env, $this->source,                     // line 66
($context["parent"] ?? null), "title", [], "any", false, false, true, 66)]), "extra_classes" => "bsi-mainmenu__overview-link"]));
                    // line 69
                    yield "    </div>

    <ul class=\"bsi-mainmenu__submenu\">
      ";
                    // line 72
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
                        // line 73
                        yield "        ";
                        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(($macros["menus"] ?? $this->throwUninitializedMacroNamespace(73))->call("l2_item", [$context["item"], ($context["l1Index"] ?? null), CoreExtension::getAttribute($this->env, $this->source, $context["loop"], "index0", [], "any", false, false, true, 73)], $context, 73, $this->source));
                        yield "
      ";
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
                    // line 75
                    yield "    </ul>

    ";
                    // line 78
                    yield "    ";
                    if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["parent"] ?? null), "content", [], "any", false, false, true, 78)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                        // line 79
                        yield "      <div class=\"bsi-mainmenu__card\">
        ";
                        // line 80
                        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["parent"] ?? null), "content", [], "any", false, false, true, 80), "html", null, true);
                        yield "
      </div>
    ";
                    }
                    // line 83
                    yield "
  </div>
";
                    return; yield;
                })(), false))) ? '' : new Markup($tmp, $this->env->getCharset());
            }, ["parent" => false, "items" => false, "panelId" => false, "l1Index" => false], false),
            "l2_item" => new \Twig\TwigMacro("l2_item", function ($item = null, $l1Index = null, $l2Index = null, ...$varargs): string|Markup {
                // line 87
                $macros = $this->macros;
                $context = [
                    "item" => $item,
                    "l1Index" => $l1Index,
                    "l2Index" => $l2Index,
                    "varargs" => $varargs,
                ] + $this->env->getGlobals();

                $blocks = [];

                return ('' === $tmp = implode('', iterator_to_array((function () use (&$context, $macros, $blocks) {
                    // line 88
                    yield "  ";
                    $macros["menus"] = $this->getMacroNamespace();
                    // line 89
                    yield "  <li class=\"bsi-mainmenu__submenu-item\">
    ";
                    // line 90
                    if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["item"] ?? null), "below", [], "any", false, false, true, 90)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                        // line 91
                        yield "      ";
                        $context["panelId"] = ((("mainMenuL3-" . ($context["l1Index"] ?? null)) . "-") . ($context["l2Index"] ?? null));
                        // line 92
                        yield "      <button type=\"button\"
              class=\"bsi-button bsi-button--primary bsi-mainmenu__collapse-toggle\"
              aria-controls=\"";
                        // line 94
                        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["panelId"] ?? null), "html", null, true);
                        yield "\"
              aria-expanded=\"false\"
              data-js-mainmenu-toggle=\"l2\">
        <span class=\"bsi-button__text\">";
                        // line 97
                        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["item"] ?? null), "title", [], "any", false, false, true, 97), "html", null, true);
                        yield "</span>
        ";
                        // line 98
                        yield from $this->load("@bsi_bund/components/icon.twig", 98)->unwrap()->yield(CoreExtension::toArray(["name" => "chevron-down", "decorative" => true, "extra_classes" => "bsi-button__icon"]));
                        // line 103
                        yield "      </button>
      ";
                        // line 104
                        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(($macros["menus"] ?? $this->throwUninitializedMacroNamespace(104))->call("l3_panel", [($context["item"] ?? null), CoreExtension::getAttribute($this->env, $this->source, ($context["item"] ?? null), "below", [], "any", false, false, true, 104), ($context["panelId"] ?? null)], $context, 104, $this->source));
                        yield "
    ";
                    } else {
                        // line 106
                        yield "      ";
                        yield from $this->load("@bsi_bund/elements/link.html.twig", 106)->unwrap()->yield(CoreExtension::toArray(["url" => CoreExtension::getAttribute($this->env, $this->source,                         // line 107
($context["item"] ?? null), "url", [], "any", false, false, true, 107), "title" => CoreExtension::getAttribute($this->env, $this->source,                         // line 108
($context["item"] ?? null), "title", [], "any", false, false, true, 108), "extra_classes" => "bsi-mainmenu__link"]));
                        // line 111
                        yield "    ";
                    }
                    // line 112
                    yield "  </li>
";
                    return; yield;
                })(), false))) ? '' : new Markup($tmp, $this->env->getCharset());
            }, ["item" => false, "l1Index" => false, "l2Index" => false], false),
            "l3_panel" => new \Twig\TwigMacro("l3_panel", function ($parent = null, $items = null, $panelId = null, ...$varargs): string|Markup {
                // line 115
                $macros = $this->macros;
                $context = [
                    "parent" => $parent,
                    "items" => $items,
                    "panelId" => $panelId,
                    "varargs" => $varargs,
                ] + $this->env->getGlobals();

                $blocks = [];

                return ('' === $tmp = implode('', iterator_to_array((function () use (&$context, $macros, $blocks) {
                    // line 116
                    yield "  <div class=\"bsi-mainmenu__panel bsi-mainmenu__panel--l3\"
       id=\"";
                    // line 117
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["panelId"] ?? null), "html", null, true);
                    yield "\"
       data-js-mainmenu-panel=\"l3\"
       hidden>
    <ul class=\"bsi-mainmenu__submenu\">
      <li class=\"bsi-mainmenu__submenu-item bsi-mainmenu__submenu-item--overview\">
        ";
                    // line 122
                    yield from $this->load("@bsi_bund/elements/link.html.twig", 122)->unwrap()->yield(CoreExtension::toArray(["url" => CoreExtension::getAttribute($this->env, $this->source,                     // line 123
($context["parent"] ?? null), "url", [], "any", false, false, true, 123), "title" => CoreExtension::getAttribute($this->env, $this->source,                     // line 124
($context["parent"] ?? null), "title", [], "any", false, false, true, 124), "extra_classes" => "bsi-mainmenu__link bsi-mainmenu__link--overview"]));
                    // line 127
                    yield "      </li>
      ";
                    // line 128
                    $context['_parent'] = $context;
                    $context['_seq'] = CoreExtension::ensureTraversable(($context["items"] ?? null));
                    foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
                        // line 129
                        yield "        <li class=\"bsi-mainmenu__submenu-item\">
          ";
                        // line 130
                        yield from $this->load("@bsi_bund/elements/link.html.twig", 130)->unwrap()->yield(CoreExtension::toArray(["url" => CoreExtension::getAttribute($this->env, $this->source,                         // line 131
$context["item"], "url", [], "any", false, false, true, 131), "title" => CoreExtension::getAttribute($this->env, $this->source,                         // line 132
$context["item"], "title", [], "any", false, false, true, 132), "extra_classes" => "bsi-mainmenu__link"]));
                        // line 135
                        yield "        </li>
      ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_key'], $context['item'], $context['_parent']);
                    $context = array_intersect_key($context, $_parent);
                    $context += $_parent;
                    // line 137
                    yield "    </ul>
  </div>
";
                    return; yield;
                })(), false))) ? '' : new Markup($tmp, $this->env->getCharset());
            }, ["parent" => false, "items" => false, "panelId" => false], false),
        ];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "themes/custom/bsi_bund/templates/menu/menu--main--header.html.twig";
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
        return array (  403 => 137,  395 => 135,  393 => 132,  392 => 131,  391 => 130,  388 => 129,  384 => 128,  381 => 127,  379 => 124,  378 => 123,  377 => 122,  369 => 117,  366 => 116,  354 => 115,  347 => 112,  344 => 111,  342 => 108,  341 => 107,  339 => 106,  334 => 104,  331 => 103,  329 => 98,  325 => 97,  319 => 94,  315 => 92,  312 => 91,  310 => 90,  307 => 89,  304 => 88,  292 => 87,  284 => 83,  278 => 80,  275 => 79,  272 => 78,  268 => 75,  250 => 73,  233 => 72,  228 => 69,  226 => 66,  225 => 65,  224 => 64,  220 => 62,  218 => 59,  217 => 58,  215 => 57,  208 => 52,  206 => 47,  202 => 46,  193 => 40,  190 => 39,  187 => 38,  174 => 37,  166 => 33,  150 => 31,  147 => 30,  145 => 27,  144 => 26,  142 => 25,  137 => 23,  132 => 21,  126 => 18,  122 => 16,  119 => 15,  117 => 14,  114 => 13,  111 => 12,  94 => 11,  91 => 10,  88 => 9,  85 => 8,  75 => 7,  64 => 114,  61 => 86,  58 => 36,  53 => 5,  50 => 4,  48 => 3,  45 => 2,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "themes/custom/bsi_bund/templates/menu/menu--main--header.html.twig", "/var/www/html/web/themes/custom/bsi_bund/templates/menu/menu--main--header.html.twig");
    }
    
    public function ensureSecurityChecked(): void
    {
        if ($this->sandbox->isSandboxed($this->source)) {
            $this->checkSecurity();
        }
    }
    
    public function checkSecurity()
    {
        static $tags = ["import" => 3, "macro" => 7, "if" => 9, "for" => 11, "set" => 12, "include" => 25];
        static $filters = ["escape" => 18, "t" => 46];
        static $functions = [];
        static $tests = [];

        try {
            $this->sandbox->checkSecurity(
                [0 => "import", 1 => "macro", 2 => "if", 3 => "for", 4 => "set", 5 => "include"],
                [0 => "escape", 1 => "t"],
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
