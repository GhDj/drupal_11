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

/* __string_template__2dcaeb7aa421493e6ca3674ecb4ee20a */
class __TwigTemplate_dffe9ce45fc06ad5bbed86a1c2715d22 extends Template
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
        // line 1
        yield "<img src=\"";
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["source"] ?? null), "html", null, true);
        yield "\"
  height=\"";
        // line 2
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ((array_key_exists("size", $context)) ? (Twig\Extension\CoreExtension::default(($context["size"] ?? null), 32)) : (32)), "html", null, true);
        yield "\" width=\"";
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ((array_key_exists("size", $context)) ? (Twig\Extension\CoreExtension::default(($context["size"] ?? null), 32)) : (32)), "html", null, true);
        yield "\"
  ";
        // line 3
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\Core\Template\TwigExtension']->withoutFilter(($context["attributes"] ?? null), "src", "height", "width", "alt"), "html", null, true);
        yield "
  alt=\"";
        // line 4
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ((array_key_exists("alt", $context)) ? (Twig\Extension\CoreExtension::default(($context["alt"] ?? null), ($context["icon_id"] ?? null))) : (($context["icon_id"] ?? null))), "html", null, true);
        yield "\">";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["source", "size", "attributes", "alt", "icon_id"]);        return; yield;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "__string_template__2dcaeb7aa421493e6ca3674ecb4ee20a";
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
        return array (  60 => 4,  56 => 3,  50 => 2,  45 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "__string_template__2dcaeb7aa421493e6ca3674ecb4ee20a", "");
    }
    
    public function ensureSecurityChecked(): void
    {
        if ($this->sandbox->isSandboxed($this->source)) {
            $this->checkSecurity();
        }
    }
    
    public function checkSecurity()
    {
        static $tags = [];
        static $filters = ["escape" => 1, "default" => 2, "without" => 3];
        static $functions = [];
        static $tests = [];

        try {
            $this->sandbox->checkSecurity(
                [],
                [0 => "escape", 1 => "default", 2 => "without"],
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
