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

/* themes/custom/bsi_bund/templates/form/form--search-global.html.twig */
class __TwigTemplate_6f3a0538b1649433b4c795e80371c5c1 extends Template
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
            'form_items' => [$this, 'block_form_items'],
            'actions' => [$this, 'block_actions'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 15
        yield "<form";
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["attributes"] ?? null), "removeClass", ["views-exposed-form"], "method", false, false, true, 15), "html", null, true);
        yield ">
  <div class=\"bsi-searchbar\">
    ";
        // line 17
        yield from $this->unwrap()->yieldBlock('form_items', $context, $blocks);
        // line 20
        yield "
    ";
        // line 21
        yield from $this->unwrap()->yieldBlock('actions', $context, $blocks);
        // line 26
        yield "  </div>
</form>
";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["attributes", "element", "has_parent"]);        return; yield;
    }

    // line 17
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_form_items(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 18
        yield "      ";
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\Core\Template\TwigExtension']->withoutFilter(($context["element"] ?? null), "actions", "#children"), "html", null, true);
        yield "
    ";
        return; yield;
    }

    // line 21
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_actions(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 22
        yield "      <div";
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $this->extensions['Drupal\Core\Template\TwigExtension']->createAttribute(((CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["element"] ?? null), "actions", [], "any", false, true, true, 22), "#attributes", [], "array", true, true, true, 22)) ? (Twig\Extension\CoreExtension::default((($_v0 = CoreExtension::getAttribute($this->env, $this->source, ($context["element"] ?? null), "actions", [], "any", false, false, true, 22)) && is_array($_v0) || $_v0 instanceof ArrayAccess && in_array($_v0::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v0["#attributes"] ?? null) : CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["element"] ?? null), "actions", [], "any", false, false, true, 22), "#attributes", [], "array", false, false, true, 22)), [])) : ([]))), "addClass", [(((($tmp =         // line 23
($context["has_parent"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("js-form-wrapper") : (""))], "method", false, false, true, 22), "html", null, true);
        // line 24
        yield ">";
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["element"] ?? null), "actions", [], "any", false, false, true, 24), "html", null, true);
        yield "</div>
    ";
        return; yield;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "themes/custom/bsi_bund/templates/form/form--search-global.html.twig";
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
        return array (  94 => 24,  92 => 23,  90 => 22,  83 => 21,  75 => 18,  68 => 17,  60 => 26,  58 => 21,  55 => 20,  53 => 17,  47 => 15,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "themes/custom/bsi_bund/templates/form/form--search-global.html.twig", "/var/www/html/web/themes/custom/bsi_bund/templates/form/form--search-global.html.twig");
    }
    
    public function ensureSecurityChecked(): void
    {
        if ($this->sandbox->isSandboxed($this->source)) {
            $this->checkSecurity();
        }
    }
    
    public function checkSecurity()
    {
        static $tags = ["block" => 17];
        static $filters = ["escape" => 15, "without" => 18, "default" => 22];
        static $functions = ["create_attribute" => 22];
        static $tests = [];

        try {
            $this->sandbox->checkSecurity(
                [0 => "block"],
                [0 => "escape", 1 => "without", 2 => "default"],
                [0 => "create_attribute"],
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
