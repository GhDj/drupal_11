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

/* themes/custom/bsi_bund/templates/paragraphs/keyfacts/paragraph--key-facts--definition-item.html.twig */
class __TwigTemplate_5df926521460314631b684f79abd1b40 extends Template
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
            'paragraph' => [$this, 'block_paragraph'],
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 7
        $context["classes"] = ["bsi-facts-box__item", "paragraph", ("paragraph--type--" . \Drupal\Component\Utility\Html::getClass(CoreExtension::getAttribute($this->env, $this->source,         // line 10
($context["paragraph"] ?? null), "bundle", [], "any", false, false, true, 10))), (((($tmp =         // line 11
($context["view_mode"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? (("paragraph--view-mode--" . \Drupal\Component\Utility\Html::getClass(($context["view_mode"] ?? null)))) : ("")), (( !(($tmp = CoreExtension::getAttribute($this->env, $this->source,         // line 12
($context["paragraph"] ?? null), "isPublished", [], "method", false, false, true, 12)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("paragraph--unpublished") : (""))];
        // line 15
        yield from $this->unwrap()->yieldBlock('paragraph', $context, $blocks);
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["paragraph", "view_mode", "attributes", "content"]);        return; yield;
    }

    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_paragraph(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 16
        yield "  <div";
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["attributes"] ?? null), "addClass", [($context["classes"] ?? null)], "method", false, false, true, 16), "html", null, true);
        yield ">
    ";
        // line 17
        yield from $this->unwrap()->yieldBlock('content', $context, $blocks);
        // line 29
        yield "  </div>
";
        return; yield;
    }

    // line 17
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 18
        yield "        <dt class=\"bsi-facts-box__item-title\">
          <div class=\"bsi-heading__wrapper\">
            <div class=\"bsi-heading bsi-heading--6 bsi-facts-box__item-heading\">
              ";
        // line 21
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "field_section_title", [], "any", false, false, true, 21), 0, [], "any", false, false, true, 21), "html", null, true);
        yield "
            </div>
          </div>
        </dt>
        <dd class=\"bsi-facts-box__item-value\">
          ";
        // line 26
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "field_section_text", [], "any", false, false, true, 26), "html", null, true);
        yield "
        </dd>
    ";
        return; yield;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "themes/custom/bsi_bund/templates/paragraphs/keyfacts/paragraph--key-facts--definition-item.html.twig";
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
        return array (  97 => 26,  89 => 21,  84 => 18,  77 => 17,  71 => 29,  69 => 17,  64 => 16,  52 => 15,  50 => 12,  49 => 11,  48 => 10,  47 => 7,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "themes/custom/bsi_bund/templates/paragraphs/keyfacts/paragraph--key-facts--definition-item.html.twig", "/var/www/html/web/themes/custom/bsi_bund/templates/paragraphs/keyfacts/paragraph--key-facts--definition-item.html.twig");
    }
    
    public function ensureSecurityChecked(): void
    {
        if ($this->sandbox->isSandboxed($this->source)) {
            $this->checkSecurity();
        }
    }
    
    public function checkSecurity()
    {
        static $tags = ["set" => 7, "block" => 15];
        static $filters = ["clean_class" => 10, "escape" => 16];
        static $functions = [];
        static $tests = [];

        try {
            $this->sandbox->checkSecurity(
                [0 => "set", 1 => "block"],
                [0 => "clean_class", 1 => "escape"],
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
