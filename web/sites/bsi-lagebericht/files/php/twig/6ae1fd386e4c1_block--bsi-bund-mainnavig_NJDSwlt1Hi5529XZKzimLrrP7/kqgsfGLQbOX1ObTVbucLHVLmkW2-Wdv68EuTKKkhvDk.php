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

/* themes/custom/bsi_bund/templates/block/block--bsi-bund-mainnavigation.html.twig */
class __TwigTemplate_3f84a39d020e50edaaa3d82c55c76646 extends Template
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
        // line 36
        $context["classes"] = ["bsi-mainmenu", "block", "block-menu", "navigation", ("menu--" . \Drupal\Component\Utility\Html::getClass(        // line 41
($context["derivative_plugin_id"] ?? null)))];
        // line 44
        $context["heading_id"] = (CoreExtension::getAttribute($this->env, $this->source, ($context["attributes"] ?? null), "id", [], "any", false, false, true, 44) . \Drupal\Component\Utility\Html::getId("-menu"));
        // line 45
        yield "

<!-- menu button -->
<button
    type=\"button\"
    class=\"bsi-button bsi-button--tertiary bsi-button--icon-only bsi-mainmenu__menu-button\"
    aria-label=\"";
        // line 51
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Toggle mainmenu"));
        yield "\"
    popovertarget=\"mainmenu\"
>
    ";
        // line 54
        yield from $this->load("@bsi_bund/components/icon.twig", 54)->unwrap()->yield(CoreExtension::merge($context, ["name" => "menu-burger", "decorative" => true, "extra_classes" => "bsi-button__icon bsi-button__icon--menu-burger"]));
        // line 59
        yield "
    ";
        // line 60
        yield from $this->load("@bsi_bund/components/icon.twig", 60)->unwrap()->yield(CoreExtension::merge($context, ["name" => "cross", "decorative" => true, "extra_classes" => "bsi-button__icon bsi-button__icon--cross"]));
        // line 65
        yield "</button>
";
        // line 67
        yield "<nav ";
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\Core\Template\TwigExtension']->withoutFilter(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["attributes"] ?? null), "addClass", [($context["classes"] ?? null)], "method", false, false, true, 67), "setAttribute", ["id", "mainmenu"], "method", false, false, true, 67), "role", "aria-labelledby"), "html", null, true);
        yield " role=\"navigation\" aria-labelledby=\"";
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["heading_id"] ?? null), "html", null, true);
        yield "\">
  ";
        // line 69
        yield "  ";
        if ( !(($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["configuration"] ?? null), "label_display", [], "any", false, false, true, 69)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 70
            yield "    ";
            $context["title_attributes"] = CoreExtension::getAttribute($this->env, $this->source, ($context["title_attributes"] ?? null), "addClass", ["visually-hidden"], "method", false, false, true, 70);
            // line 71
            yield "  ";
        }
        // line 72
        yield "  ";
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["title_prefix"] ?? null), "html", null, true);
        yield "
  <span";
        // line 73
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["title_attributes"] ?? null), "setAttribute", ["id", ($context["heading_id"] ?? null)], "method", false, false, true, 73), "html", null, true);
        yield ">";
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["configuration"] ?? null), "label", [], "any", false, false, true, 73), "html", null, true);
        yield "</span>
  ";
        // line 74
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["title_suffix"] ?? null), "html", null, true);
        yield "

  ";
        // line 77
        yield "  ";
        yield from $this->unwrap()->yieldBlock('content', $context, $blocks);
        // line 80
        yield "</nav>
";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["derivative_plugin_id", "attributes", "configuration", "title_prefix", "title_suffix", "content"]);        return; yield;
    }

    // line 77
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 78
        yield "    ";
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["content"] ?? null), "html", null, true);
        yield "
  ";
        return; yield;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "themes/custom/bsi_bund/templates/block/block--bsi-bund-mainnavigation.html.twig";
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
        return array (  124 => 78,  117 => 77,  110 => 80,  107 => 77,  102 => 74,  96 => 73,  91 => 72,  88 => 71,  85 => 70,  82 => 69,  75 => 67,  72 => 65,  70 => 60,  67 => 59,  65 => 54,  59 => 51,  51 => 45,  49 => 44,  47 => 41,  46 => 36,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "themes/custom/bsi_bund/templates/block/block--bsi-bund-mainnavigation.html.twig", "/var/www/html/web/themes/custom/bsi_bund/templates/block/block--bsi-bund-mainnavigation.html.twig");
    }
    
    public function ensureSecurityChecked(): void
    {
        if ($this->sandbox->isSandboxed($this->source)) {
            $this->checkSecurity();
        }
    }
    
    public function checkSecurity()
    {
        static $tags = ["set" => 36, "include" => 54, "if" => 69, "block" => 77];
        static $filters = ["clean_class" => 41, "clean_id" => 44, "t" => 51, "escape" => 67, "without" => 67];
        static $functions = [];
        static $tests = [];

        try {
            $this->sandbox->checkSecurity(
                [0 => "set", 1 => "include", 2 => "if", 3 => "block"],
                [0 => "clean_class", 1 => "clean_id", 2 => "t", 3 => "escape", 4 => "without"],
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
