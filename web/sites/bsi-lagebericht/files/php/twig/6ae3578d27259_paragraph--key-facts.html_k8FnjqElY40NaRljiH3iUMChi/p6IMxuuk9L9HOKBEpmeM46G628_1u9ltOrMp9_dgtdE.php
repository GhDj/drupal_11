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

/* themes/custom/bsi_bund/templates/paragraphs/keyfacts/paragraph--key-facts.html.twig */
class __TwigTemplate_38a79b679f5e210f7df5cb48147de018 extends Template
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
        $context["classes"] = ["bsi-block", "paragraph", ("paragraph--type--" . \Drupal\Component\Utility\Html::getClass(CoreExtension::getAttribute($this->env, $this->source,         // line 10
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
        yield " <div";
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["attributes"] ?? null), "addClass", [($context["classes"] ?? null)], "method", false, false, true, 16), "html", null, true);
        yield " >
  <div class=\"bsi-grid bsi-facts-box\" data-theme=\x27";
        // line 17
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, (($_v0 = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "field_display_variant", [], "any", false, false, true, 17), 0, [], "any", false, false, true, 17)) && is_array($_v0) || $_v0 instanceof ArrayAccess && in_array($_v0::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v0["#preset_option"] ?? null) : CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "field_display_variant", [], "any", false, false, true, 17), 0, [], "any", false, false, true, 17), "#preset_option", [], "array", false, false, true, 17)), "html", null, true);
        yield "\x27>
    ";
        // line 18
        yield from $this->unwrap()->yieldBlock('content', $context, $blocks);
        // line 48
        yield "    </div>
  </div>
";
        return; yield;
    }

    // line 18
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 19
        yield "      ";
        // line 20
        yield "      ";
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["paragraph"] ?? null), "field_display_icon", [], "any", false, false, true, 20), "value", [], "any", false, false, true, 20)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 21
            yield "        <div class=\"bsi-facts-box__icon-wrap\">
          ";
            // line 22
            yield from $this->load("@bsi_bund/components/icon.twig", 22)->unwrap()->yield(CoreExtension::toArray(["name" => "info-circle", "decorative" => true, "extra_classes" => "bsi-facts-box__icon"]));
            // line 27
            yield "        </div>
      ";
        }
        // line 29
        yield "
      <div class=\"bsi-facts-box__content\">

        ";
        // line 33
        yield "        <div class=\"bsi-heading__wrapper\">
            ";
        // line 34
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "field_section_title", [], "any", false, false, true, 34), "html", null, true);
        yield "
        </div>

        ";
        // line 38
        yield "        ";
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "field_section_text", [], "any", false, false, true, 38)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 39
            yield "          <div class=\"bsi-facts-box__intro-text\">";
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "field_section_text", [], "any", false, false, true, 39), "html", null, true);
            yield "</div>
        ";
        }
        // line 41
        yield "
        ";
        // line 43
        yield "        <dl class=\"bsi-facts-box__list\">
          ";
        // line 44
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "field_items", [], "any", false, false, true, 44), "html", null, true);
        yield "
        </dl>
      </div>
    ";
        return; yield;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "themes/custom/bsi_bund/templates/paragraphs/keyfacts/paragraph--key-facts.html.twig";
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
        return array (  132 => 44,  129 => 43,  126 => 41,  120 => 39,  117 => 38,  111 => 34,  108 => 33,  103 => 29,  99 => 27,  97 => 22,  94 => 21,  91 => 20,  89 => 19,  82 => 18,  75 => 48,  73 => 18,  69 => 17,  64 => 16,  52 => 15,  50 => 12,  49 => 11,  48 => 10,  47 => 7,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "themes/custom/bsi_bund/templates/paragraphs/keyfacts/paragraph--key-facts.html.twig", "/var/www/html/web/themes/custom/bsi_bund/templates/paragraphs/keyfacts/paragraph--key-facts.html.twig");
    }
    
    public function ensureSecurityChecked(): void
    {
        if ($this->sandbox->isSandboxed($this->source)) {
            $this->checkSecurity();
        }
    }
    
    public function checkSecurity()
    {
        static $tags = ["set" => 7, "block" => 15, "if" => 20, "include" => 22];
        static $filters = ["clean_class" => 10, "escape" => 16];
        static $functions = [];
        static $tests = [];

        try {
            $this->sandbox->checkSecurity(
                [0 => "set", 1 => "block", 2 => "if", 3 => "include"],
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
