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

/* themes/custom/bsi_bund/templates/block/block--system-branding-block.html.twig */
class __TwigTemplate_63ce92dc75f1cdffd78152b3e5abc55a extends Template
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
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 13
        yield from $this->unwrap()->yieldBlock('content', $context, $blocks);
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["site_logo", "directory"]);        return; yield;
    }

    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 14
        yield "  ";
        if ((($tmp = ($context["site_logo"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 15
            yield "    <div class=\"bsi-header__logo\">
      <div class=\"bsi-header__logo--desktop\">
        <a href=\"";
            // line 17
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar($this->extensions['Drupal\Core\Template\TwigExtension']->getPath("<front>"));
            yield "\" rel=\"home\" class=\"site-logo\">
          <img src=\"";
            // line 18
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["site_logo"] ?? null), "html", null, true);
            yield "\" alt=\"";
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Home"));
            yield "\" fetchpriority=\"high\" />
        </a>
      </div>
      <div class=\"bsi-header__logo--mobile\">
        <a href=\"";
            // line 22
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar($this->extensions['Drupal\Core\Template\TwigExtension']->getPath("<front>"));
            yield "\" rel=\"home\" class=\"site-logo\">
          <img src=\"/";
            // line 23
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["directory"] ?? null), "html", null, true);
            yield "/logo_mobile.svg\" alt=\"";
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Home"));
            yield "\" fetchpriority=\"high\" />
        </a>
      </div>
    </div>
  ";
        }
        return; yield;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "themes/custom/bsi_bund/templates/block/block--system-branding-block.html.twig";
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
        return array (  82 => 23,  78 => 22,  69 => 18,  65 => 17,  61 => 15,  58 => 14,  46 => 13,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "themes/custom/bsi_bund/templates/block/block--system-branding-block.html.twig", "/var/www/html/web/themes/custom/bsi_bund/templates/block/block--system-branding-block.html.twig");
    }
    
    public function ensureSecurityChecked(): void
    {
        if ($this->sandbox->isSandboxed($this->source)) {
            $this->checkSecurity();
        }
    }
    
    public function checkSecurity()
    {
        static $tags = ["block" => 13, "if" => 14];
        static $filters = ["escape" => 18, "t" => 18];
        static $functions = ["path" => 17];
        static $tests = [];

        try {
            $this->sandbox->checkSecurity(
                [0 => "block", 1 => "if"],
                [0 => "escape", 1 => "t"],
                [0 => "path"],
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
