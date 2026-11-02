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

/* themes/custom/bsi_bund/templates/page.html.twig */
class __TwigTemplate_b19971b400c2600e8af6e4128c789f98 extends Template
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
        // line 42
        yield "<div class=\"bsi-page-grid\">
    ";
        // line 44
        yield "    ";
        yield from $this->load("@bsi_bund/templates/elements/header.html.twig", 44)->unwrap()->yield($context);
        // line 45
        yield "
    ";
        // line 47
        yield "    ";
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "breadcrumb", [], "any", false, false, true, 47)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 48
            yield "        ";
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "breadcrumb", [], "any", false, false, true, 48), "html", null, true);
            yield "
    ";
        }
        // line 50
        yield "
    ";
        // line 52
        yield "    <div class=\"visually-hidden\"><a id=\"main-content\" tabindex=\"-1\"></a></div>

    ";
        // line 55
        yield "    ";
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "pre_content", [], "any", false, false, true, 55)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 56
            yield "        <div class=\"bsi-page-grid__stage\">
            ";
            // line 57
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\Core\Template\TwigExtension']->withoutFilter(CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "pre_content", [], "any", false, false, true, 57), "bsi_bund_jumplinks"), "html", null, true);
            yield "
        </div>

        ";
            // line 61
            yield "        ";
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "pre_content", [], "any", false, false, true, 61), "bsi_bund_jumplinks", [], "any", false, false, true, 61), "html", null, true);
            yield "
    ";
        }
        // line 63
        yield "
    ";
        // line 65
        yield "    ";
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "highlighted", [], "any", false, false, true, 65), "html", null, true);
        yield "

    ";
        // line 68
        yield "    <main class=\"bsi-page-grid__main\">
        ";
        // line 70
        yield "        ";
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "help", [], "any", false, false, true, 70)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 71
            yield "            ";
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "help", [], "any", false, false, true, 71), "html", null, true);
            yield "
        ";
        }
        // line 73
        yield "
        ";
        // line 75
        yield "        ";
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "content", [], "any", false, false, true, 75), "html", null, true);
        yield "
    </main>

    ";
        // line 79
        yield "    <footer class=\"bsi-footer\">
        <div class=\"bsi-grid\">
            <div class=\"bsi-footer__top\">";
        // line 81
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "footer", [], "any", false, false, true, 81), "html", null, true);
        yield "</div>
            <hr>
            <div class=\"bsi-footer__bottom\">";
        // line 83
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "legal", [], "any", false, false, true, 83), "html", null, true);
        yield "</div>
        </div>
    </footer>
</div>
";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["page"]);        return; yield;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "themes/custom/bsi_bund/templates/page.html.twig";
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
        return array (  128 => 83,  123 => 81,  119 => 79,  112 => 75,  109 => 73,  103 => 71,  100 => 70,  97 => 68,  91 => 65,  88 => 63,  82 => 61,  76 => 57,  73 => 56,  70 => 55,  66 => 52,  63 => 50,  57 => 48,  54 => 47,  51 => 45,  48 => 44,  45 => 42,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "themes/custom/bsi_bund/templates/page.html.twig", "/var/www/html/web/themes/custom/bsi_bund/templates/page.html.twig");
    }
    
    public function ensureSecurityChecked(): void
    {
        if ($this->sandbox->isSandboxed($this->source)) {
            $this->checkSecurity();
        }
    }
    
    public function checkSecurity()
    {
        static $tags = ["include" => 44, "if" => 47];
        static $filters = ["escape" => 48, "without" => 57];
        static $functions = [];
        static $tests = [];

        try {
            $this->sandbox->checkSecurity(
                [0 => "include", 1 => "if"],
                [0 => "escape", 1 => "without"],
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
