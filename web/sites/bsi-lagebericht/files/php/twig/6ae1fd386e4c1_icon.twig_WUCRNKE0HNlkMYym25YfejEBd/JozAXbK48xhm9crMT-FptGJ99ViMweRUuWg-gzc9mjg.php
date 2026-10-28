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

/* @bsi_bund/components/icon.twig */
class __TwigTemplate_922379d20c44a0d00cd2f507de4d96e3 extends Template
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
        // line 23
        yield "
";
        // line 24
        $context["icon_name"] = ((array_key_exists("name", $context)) ? (Twig\Extension\CoreExtension::default(($context["name"] ?? null), "")) : (""));
        // line 25
        $context["decorative"] = (((array_key_exists("decorative", $context) &&  !(null === $context["decorative"]))) ? ($context["decorative"]) : (true));
        // line 26
        $context["extra_classes"] = ((array_key_exists("extra_classes", $context)) ? (Twig\Extension\CoreExtension::default(($context["extra_classes"] ?? null), "")) : (""));
        // line 27
        yield "
";
        // line 28
        if ((($tmp = ($context["icon_name"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 29
            yield "  <svg
    class=\"bsi-icon ";
            // line 30
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["extra_classes"] ?? null), "html", null, true);
            yield "\"
    ";
            // line 31
            if ((($tmp = ($context["decorative"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 32
                yield "      aria-hidden=\"true\"
      focusable=\"false\"
    ";
            } else {
                // line 35
                yield "      role=\"img\"
    ";
            }
            // line 37
            yield "  >
    <use href=\"";
            // line 38
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\Core\Template\TwigExtension']->getFileUrl("themes/custom/bsi_bund/dist/icons.svg"), "html", null, true);
            yield "#";
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["icon_name"] ?? null), "html", null, true);
            yield "\"></use>
  </svg>
";
        }
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["name"]);        return; yield;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "@bsi_bund/components/icon.twig";
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
        return array (  80 => 38,  77 => 37,  73 => 35,  68 => 32,  66 => 31,  62 => 30,  59 => 29,  57 => 28,  54 => 27,  52 => 26,  50 => 25,  48 => 24,  45 => 23,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "@bsi_bund/components/icon.twig", "/var/www/html/web/themes/custom/bsi_bund/templates/components/icon.twig");
    }
    
    public function ensureSecurityChecked(): void
    {
        if ($this->sandbox->isSandboxed($this->source)) {
            $this->checkSecurity();
        }
    }
    
    public function checkSecurity()
    {
        static $tags = ["set" => 24, "if" => 28];
        static $filters = ["default" => 24, "escape" => 30];
        static $functions = ["file_url" => 38];
        static $tests = [];

        try {
            $this->sandbox->checkSecurity(
                [0 => "set", 1 => "if"],
                [0 => "default", 1 => "escape"],
                [0 => "file_url"],
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
