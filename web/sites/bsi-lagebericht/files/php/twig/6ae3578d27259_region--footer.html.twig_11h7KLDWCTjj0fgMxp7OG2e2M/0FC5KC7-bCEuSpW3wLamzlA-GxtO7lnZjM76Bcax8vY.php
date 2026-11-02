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

/* themes/custom/bsi_bund/templates/layout/region--footer.html.twig */
class __TwigTemplate_9bc5afb93f4e4bf67981f5e74e033706 extends Template
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
        // line 16
        $context["classes"] = ["region", ("region-" . \Drupal\Component\Utility\Html::getClass(        // line 18
($context["region"] ?? null)))];
        // line 21
        yield "
";
        // line 22
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["elements"] ?? null), "bsi_bund_quick_navigation", [], "any", false, false, true, 22), "html", null, true);
        yield "
<div class=\"bsi-footer__content-wrapper\">
  <div class=\"bsi-footer__content\">
    ";
        // line 25
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(Twig\Extension\CoreExtension::filter($this->env, $this->env->hasExtension(\Twig\Extension\SandboxExtension::class) && $this->env->getExtension(\Twig\Extension\SandboxExtension::class)->getChecker()->isSandboxed($this->source), $this->extensions['Drupal\Core\Template\TwigExtension']->withoutFilter($this->extensions['Drupal\Core\Template\TwigExtension']->withoutFilter(($context["elements"] ?? null), "bsi_bund_quick_navigation"), "bsi_bund_social_navigation"), function ($__v__, $__k__) use ($context, $macros) { $context["v"] = $__v__; $context["k"] = $__k__; return (Twig\Extension\CoreExtension::first($this->env->getCharset(), ($context["k"] ?? null)) != "#"); }));
        foreach ($context['_seq'] as $context["key"] => $context["child"]) {
            // line 26
            yield "      ";
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $context["child"], "html", null, true);
            yield "
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['key'], $context['child'], $context['_parent']);
        $context = array_intersect_key($context, $_parent);
        $context += $_parent;
        // line 28
        yield "  </div>
   ";
        // line 29
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["elements"] ?? null), "bsi_bund_social_navigation", [], "any", false, false, true, 29), "html", null, true);
        yield "
</div>";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["region", "elements", "k"]);        return; yield;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "themes/custom/bsi_bund/templates/layout/region--footer.html.twig";
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
        return array (  74 => 29,  71 => 28,  61 => 26,  57 => 25,  51 => 22,  48 => 21,  46 => 18,  45 => 16,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "themes/custom/bsi_bund/templates/layout/region--footer.html.twig", "/var/www/html/web/themes/custom/bsi_bund/templates/layout/region--footer.html.twig");
    }
    
    public function ensureSecurityChecked(): void
    {
        if ($this->sandbox->isSandboxed($this->source)) {
            $this->checkSecurity();
        }
    }
    
    public function checkSecurity()
    {
        static $tags = ["set" => 16, "for" => 25];
        static $filters = ["clean_class" => 18, "escape" => 22, "filter" => 25, "without" => 25, "first" => 25];
        static $functions = [];
        static $tests = [];

        try {
            $this->sandbox->checkSecurity(
                [0 => "set", 1 => "for"],
                [0 => "clean_class", 1 => "escape", 2 => "filter", 3 => "without", 4 => "first"],
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
