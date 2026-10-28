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

/* themes/custom/bsi_bund/templates/block/block--bsi-bund-site-search.html.twig */
class __TwigTemplate_41a9e1372dc95dee8ecefc5934c951e8 extends Template
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
        // line 30
        $context["classes"] = ["bsi-grid", "block", ("block-" . \Drupal\Component\Utility\Html::getClass(CoreExtension::getAttribute($this->env, $this->source,         // line 33
($context["configuration"] ?? null), "provider", [], "any", false, false, true, 33))), ("block-" . \Drupal\Component\Utility\Html::getClass(        // line 34
($context["plugin_id"] ?? null))), "bsi-grid", "bsi-searchmenu--header"];
        // line 39
        yield " <button type=\"button\" class=\"bsi-button bsi-button--tertiary bsi-button--icon-only bsi-header__search-button\" aria-label=\"Suche\" popovertarget=\"header-search\">
    ";
        // line 40
        yield from $this->load("@bsi_bund/components/icon.twig", 40)->unwrap()->yield(CoreExtension::merge($context, ["name" => "search", "decorative" => true, "extra_classes" => "bsi-button__icon bsi-button__icon--search"]));
        // line 45
        yield "</button>
<div class=\"bsi-searchmenu\" id=\"header-search\" popover=\"\">
    <div";
        // line 47
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["attributes"] ?? null), "addClass", [($context["classes"] ?? null)], "method", false, false, true, 47), "html", null, true);
        yield ">
        <div class=\"bsi-searchmenu__searchbar-wrapper\">
            ";
        // line 49
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["title_prefix"] ?? null), "html", null, true);
        yield "
            ";
        // line 50
        if ((($tmp = ($context["label"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 51
            yield "                <span";
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["title_attributes"] ?? null), "addClass", ["bsi-searchbar__title"], "method", false, false, true, 51), "html", null, true);
            yield ">";
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["label"] ?? null), "html", null, true);
            yield "</span>
            ";
        }
        // line 53
        yield "            ";
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["title_suffix"] ?? null), "html", null, true);
        yield "

            ";
        // line 55
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\Core\Template\TwigExtension']->withoutFilter(($context["content"] ?? null), "suggestions"), "html", null, true);
        yield "
        </div>
        <nav aria-label=\"";
        // line 57
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Quicklinks"));
        yield "\">
            <p class=\"bsi-heading bsi-heading--6 bsi-searchmenu__title\">";
        // line 58
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Frequently searched"));
        yield "</p>
            <ul class=\"bsi-searchmenu__list\">
                ";
        // line 60
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "suggestions", [], "any", false, false, true, 60), "html", null, true);
        yield "
            </ul>
        </nav>

    </div>
</div>
";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["configuration", "plugin_id", "attributes", "title_prefix", "label", "title_attributes", "title_suffix", "content"]);        return; yield;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "themes/custom/bsi_bund/templates/block/block--bsi-bund-site-search.html.twig";
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
        return array (  97 => 60,  92 => 58,  88 => 57,  83 => 55,  77 => 53,  69 => 51,  67 => 50,  63 => 49,  58 => 47,  54 => 45,  52 => 40,  49 => 39,  47 => 34,  46 => 33,  45 => 30,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "themes/custom/bsi_bund/templates/block/block--bsi-bund-site-search.html.twig", "/var/www/html/web/themes/custom/bsi_bund/templates/block/block--bsi-bund-site-search.html.twig");
    }
    
    public function ensureSecurityChecked(): void
    {
        if ($this->sandbox->isSandboxed($this->source)) {
            $this->checkSecurity();
        }
    }
    
    public function checkSecurity()
    {
        static $tags = ["set" => 30, "include" => 40, "if" => 50];
        static $filters = ["clean_class" => 33, "escape" => 47, "without" => 55, "t" => 57];
        static $functions = [];
        static $tests = [];

        try {
            $this->sandbox->checkSecurity(
                [0 => "set", 1 => "include", 2 => "if"],
                [0 => "clean_class", 1 => "escape", 2 => "without", 3 => "t"],
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
