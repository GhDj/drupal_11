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

/* themes/custom/bsi_bund/templates/content/node/node--opener--static.html.twig */
class __TwigTemplate_413135fe28cd39e149334621d79c99d0 extends Template
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
        // line 4
        $context["entity"] = (($_v0 = ($context["elements"] ?? null)) && is_array($_v0) || $_v0 instanceof ArrayAccess && in_array($_v0::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v0[(($_v1 = ("#" . (($_v2 = ($context["elements"] ?? null)) && is_array($_v2) || $_v2 instanceof ArrayAccess && in_array($_v2::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v2["#entity_type"] ?? null) : CoreExtension::getAttribute($this->env, $this->source, ($context["elements"] ?? null), "#entity_type", [], "array", false, false, true, 4)))) instanceof \Stringable && (is_array($_v0) || $_v0 instanceof \ArrayObject || $_v0 instanceof \ArrayIterator) ? (string) $_v1 : $_v1)] ?? null) : CoreExtension::getAttribute($this->env, $this->source, ($context["elements"] ?? null), ("#" . (($_v3 = ($context["elements"] ?? null)) && is_array($_v3) || $_v3 instanceof ArrayAccess && in_array($_v3::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v3["#entity_type"] ?? null) : CoreExtension::getAttribute($this->env, $this->source, ($context["elements"] ?? null), "#entity_type", [], "array", false, false, true, 4))), [], "array", false, false, true, 4));
        // line 5
        $context["entity_type_id"] = CoreExtension::getAttribute($this->env, $this->source, ($context["entity"] ?? null), "getEntityTypeId", [], "method", false, false, true, 5);
        // line 7
        $context["classes"] = ["bsi-opener", "bsi-opener--left-inside",         // line 10
($context["entity_type_id"] ?? null), ((        // line 11
($context["entity_type_id"] ?? null) . "--type-") . \Drupal\Component\Utility\Html::getClass(CoreExtension::getAttribute($this->env, $this->source, ($context["entity"] ?? null), "bundle", [], "method", false, false, true, 11))), (( !(($tmp = CoreExtension::getAttribute($this->env, $this->source,         // line 12
($context["entity"] ?? null), "isPublished", [], "method", false, false, true, 12)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ((($context["entity_type_id"] ?? null) . "--unpublished")) : ("")), (((($tmp =         // line 13
($context["view_mode"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? (((($context["entity_type_id"] ?? null) . "--view-mode-") . \Drupal\Component\Utility\Html::getClass(($context["view_mode"] ?? null)))) : ("")), (((($tmp = CoreExtension::getAttribute($this->env, $this->source,         // line 15
($context["node"] ?? null), "isPromoted", [], "method", false, false, true, 15)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("node--promoted") : ("")), (((($tmp = CoreExtension::getAttribute($this->env, $this->source,         // line 16
($context["node"] ?? null), "isSticky", [], "method", false, false, true, 16)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("node--sticky") : (""))];
        // line 19
        yield "
";
        // line 21
        $context["job_facts"] = [["icon" => "pin", "field" => "field_location"], ["icon" => "diploma", "field" => "field_degree"], ["icon" => "calendar-date", "field" => "field_start_date"], ["icon" => "document", "field" => "field_duration"], ["icon" => "job", "field" => "field_employment_model"], ["icon" => "money", "field" => "field_salary_group"], ["icon" => "calendar-event", "field" => "field_deadline"], ["icon" => "hash-mark", "field" => "field_code"]];
        // line 31
        yield "
";
        // line 32
        if ((($context["entity_type_id"] ?? null) == "node")) {
            // line 33
            yield "  ";
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\Core\Template\TwigExtension']->attachLibrary("claro/classy.node"), "html", null, true);
            yield "
";
        }
        // line 35
        yield "
";
        // line 36
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "field_media", [], "any", false, false, true, 36), 0, [], "any", false, false, true, 36)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 37
            yield "
  <div";
            // line 38
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["attributes"] ?? null), "addClass", [($context["classes"] ?? null)], "method", false, false, true, 38), "html", null, true);
            yield ">
    ";
            // line 39
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["title_prefix"] ?? null), "html", null, true);
            yield "
    ";
            // line 45
            yield "    ";
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["title_suffix"] ?? null), "html", null, true);
            yield "

    <div class=\"bsi-opener__content-wrapper\">
      <figure class=\"bsi-image bsi-opener__image\">
        ";
            // line 49
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "field_media", [], "any", false, false, true, 49), "html", null, true);
            yield "
      </figure>


      <div class=\"bsi-opener__content-grid\">
        <div class=\"bsi-opener__content\">

          ";
            // line 56
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "title", [], "any", false, false, true, 56), 0, [], "any", false, false, true, 56)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 57
                yield "            <h1 class=\"bsi-heading bsi-heading--2 bsi-opener__title\">
              ";
                // line 58
                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "title", [], "any", false, false, true, 58), 0, [], "any", false, false, true, 58), "html", null, true);
                yield "
            </h1>
          ";
            }
            // line 61
            yield "
          ";
            // line 62
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "field_article_type", [], "any", false, false, true, 62), 0, [], "any", false, false, true, 62)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 63
                yield "            <div class=\"bsi-opener__text\">
              ";
                // line 64
                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "field_article_type", [], "any", false, false, true, 64), 0, [], "any", false, false, true, 64), "html", null, true);
                yield "
            </div>
          ";
            }
            // line 67
            yield "
          ";
            // line 68
            if ((($tmp = (($_v4 = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "body", [], "any", false, false, true, 68), 0, [], "any", false, false, true, 68)) && is_array($_v4) || $_v4 instanceof ArrayAccess && in_array($_v4::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v4["#text"] ?? null) : CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "body", [], "any", false, false, true, 68), 0, [], "any", false, false, true, 68), "#text", [], "array", false, false, true, 68))) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 69
                yield "            <div class=\"bsi-opener__text\">
              ";
                // line 70
                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "body", [], "any", false, false, true, 70), 0, [], "any", false, false, true, 70), "html", null, true);
                yield "
            </div>
          ";
            }
            // line 73
            yield "          ";
            // line 74
            yield "          ";
            if ((($context["entity_type_id"] ?? null) == "media")) {
                // line 75
                yield "            <h1 class=\"bsi-heading bsi-heading--2\">";
                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["name"] ?? null), "html", null, true);
                yield "</h1>
          ";
            } else {
                // line 77
                yield "            ";
                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\Core\Template\TwigExtension']->withoutFilter(($context["content"] ?? null), "title", "field_media", "body", "field_article_type", "group_tagline", "group_header"), "html", null, true);
                yield "
          ";
            }
            // line 79
            yield "        </div>
      </div>
    </div>

    ";
            // line 84
            yield "    ";
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "group_header", [], "any", false, false, true, 84), "field_location", [], "any", false, false, true, 84), 0, [], "any", false, false, true, 84)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 85
                yield "      <div class=\"bsi-job-facts\">
        <div class=\"bsi-job-facts__content\">
          <ul class=\"bsi-job-facts__list\">
            ";
                // line 88
                $context['_parent'] = $context;
                $context['_seq'] = CoreExtension::ensureTraversable(($context["job_facts"] ?? null));
                $context['loop'] = [
                  'parent' => $context['_parent'],
                  'index0' => 0,
                  'index'  => 1,
                  'first'  => true,
                ];
                if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
                    $length = count($context['_seq']);
                    $context['loop']['revindex0'] = $length - 1;
                    $context['loop']['revindex'] = $length;
                    $context['loop']['length'] = $length;
                    $context['loop']['last'] = 1 === $length;
                }
                foreach ($context['_seq'] as $context["_key"] => $context["fact"]) {
                    // line 89
                    yield "              <li class=\"bsi-job-facts__item\">
                <div class=\"bsi-job-facts__icon\">
                  ";
                    // line 91
                    yield from $this->load("@bsi_bund/templates/components/icon.twig", 91)->unwrap()->yield(CoreExtension::merge($context, ["name" => CoreExtension::getAttribute($this->env, $this->source,                     // line 92
$context["fact"], "icon", [], "any", false, false, true, 92), "decorative" => true]));
                    // line 95
                    yield "                </div>
                <div class=\"bsi-job-facts__content\">
                  <div class=\"bsi-job-facts__label\">";
                    // line 97
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, (($_v5 = (($_v6 = CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "group_header", [], "any", false, false, true, 97)) && is_array($_v6) || $_v6 instanceof ArrayAccess && in_array($_v6::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v6[(($_v7 = CoreExtension::getAttribute($this->env, $this->source, $context["fact"], "field", [], "any", false, false, true, 97)) instanceof \Stringable && (is_array($_v6) || $_v6 instanceof \ArrayObject || $_v6 instanceof \ArrayIterator) ? (string) $_v7 : $_v7)] ?? null) : CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "group_header", [], "any", false, false, true, 97), CoreExtension::getAttribute($this->env, $this->source, $context["fact"], "field", [], "any", false, false, true, 97), [], "array", false, false, true, 97))) && is_array($_v5) || $_v5 instanceof ArrayAccess && in_array($_v5::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v5["#title"] ?? null) : CoreExtension::getAttribute($this->env, $this->source, (($_v8 = CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "group_header", [], "any", false, false, true, 97)) && is_array($_v8) || $_v8 instanceof ArrayAccess && in_array($_v8::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v8[(($_v9 = CoreExtension::getAttribute($this->env, $this->source, $context["fact"], "field", [], "any", false, false, true, 97)) instanceof \Stringable && (is_array($_v8) || $_v8 instanceof \ArrayObject || $_v8 instanceof \ArrayIterator) ? (string) $_v9 : $_v9)] ?? null) : CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "group_header", [], "any", false, false, true, 97), CoreExtension::getAttribute($this->env, $this->source, $context["fact"], "field", [], "any", false, false, true, 97), [], "array", false, false, true, 97)), "#title", [], "array", false, false, true, 97)), "html", null, true);
                    yield "</div>
                  <div class=\"bsi-job-facts__value\">";
                    // line 98
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, (($_v10 = CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "group_header", [], "any", false, false, true, 98)) && is_array($_v10) || $_v10 instanceof ArrayAccess && in_array($_v10::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v10[(($_v11 = CoreExtension::getAttribute($this->env, $this->source, $context["fact"], "field", [], "any", false, false, true, 98)) instanceof \Stringable && (is_array($_v10) || $_v10 instanceof \ArrayObject || $_v10 instanceof \ArrayIterator) ? (string) $_v11 : $_v11)] ?? null) : CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "group_header", [], "any", false, false, true, 98), CoreExtension::getAttribute($this->env, $this->source, $context["fact"], "field", [], "any", false, false, true, 98), [], "array", false, false, true, 98)), 0, [], "any", false, false, true, 98), "html", null, true);
                    yield "</div>
                </div>
              </li>
            ";
                    ++$context['loop']['index0'];
                    ++$context['loop']['index'];
                    $context['loop']['first'] = false;
                    if (isset($context['loop']['revindex0'], $context['loop']['revindex'])) {
                        --$context['loop']['revindex0'];
                        --$context['loop']['revindex'];
                        $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                    }
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_key'], $context['fact'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent);
                $context += $_parent;
                // line 102
                yield "          </ul>
        </div>
      </div>
    ";
            }
            // line 106
            yield "  </div>

  ";
            // line 109
            yield "  ";
            if ((((($tmp = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "group_tagline", [], "any", false, false, true, 109), "field_location", [], "any", false, false, true, 109), 0, [], "any", false, false, true, 109)) && $tmp instanceof Markup ? (string) $tmp : $tmp) || (($tmp = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "group_tagline", [], "any", false, false, true, 109), "created", [], "any", false, false, true, 109), 0, [], "any", false, false, true, 109)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) || (($tmp = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "group_tagline", [], "any", false, false, true, 109), "field_date_range", [], "any", false, false, true, 109), 0, [], "any", false, false, true, 109)) && $tmp instanceof Markup ? (string) $tmp : $tmp))) {
                // line 110
                yield "    <ul class=\"bsi-opener__badges\">
      ";
                // line 112
                yield "      ";
                if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "field_article_type", [], "any", false, false, true, 112), 0, [], "any", false, false, true, 112)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    // line 113
                    yield "        <li class=\"bsi-opener__badge-item\">
          <span class=\"bsi-opener__badge-prefix\">";
                    // line 114
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Type"));
                    yield "</span>
          <span class=\"bsi-badge bsi-opener__badge bsi-badge--date\">";
                    // line 115
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "field_article_type", [], "any", false, false, true, 115), 0, [], "any", false, false, true, 115), "html", null, true);
                    yield "</span>
        </li>
      ";
                }
                // line 118
                yield "
      ";
                // line 120
                yield "      ";
                if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "group_tagline", [], "any", false, false, true, 120), "field_date_range", [], "any", false, false, true, 120), 0, [], "any", false, false, true, 120)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    // line 121
                    yield "        <li class=\"bsi-opener__badge-item\">
          <span class=\"bsi-opener__badge-prefix\">";
                    // line 122
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Begin"));
                    yield "</span>
          <span class=\"bsi-badge bsi-opener__badge bsi-badge--date\">";
                    // line 123
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "group_tagline", [], "any", false, false, true, 123), "field_date_range", [], "any", false, false, true, 123), 0, [], "any", false, false, true, 123), "start_date", [], "any", false, false, true, 123), "html", null, true);
                    yield "</span>
        </li>

        <li class=\"bsi-opener__badge-item\">
          <span class=\"bsi-opener__badge-prefix\">";
                    // line 127
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("End"));
                    yield "</span>
          <span class=\"bsi-badge bsi-opener__badge bsi-badge--date\">";
                    // line 128
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "group_tagline", [], "any", false, false, true, 128), "field_date_range", [], "any", false, false, true, 128), 0, [], "any", false, false, true, 128), "end_date", [], "any", false, false, true, 128), "html", null, true);
                    yield "</span>
        </li>
      ";
                }
                // line 131
                yield "
      ";
                // line 133
                yield "      ";
                if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "group_tagline", [], "any", false, false, true, 133), "field_location", [], "any", false, false, true, 133), 0, [], "any", false, false, true, 133)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    // line 134
                    yield "        <li class=\"bsi-opener__badge-item\">
          <span class=\"bsi-opener__badge-prefix\">";
                    // line 135
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Location"));
                    yield "</span>
          <span class=\"bsi-badge bsi-opener__badge bsi-badge--date\">";
                    // line 136
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "group_tagline", [], "any", false, false, true, 136), "field_location", [], "any", false, false, true, 136), 0, [], "any", false, false, true, 136), "html", null, true);
                    yield "</span>
        </li>
      ";
                }
                // line 139
                yield "
      ";
                // line 141
                yield "      ";
                if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "group_tagline", [], "any", false, false, true, 141), "created", [], "any", false, false, true, 141), 0, [], "any", false, false, true, 141)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    // line 142
                    yield "        <li class=\"bsi-opener__badge-item\">
          <span class=\"bsi-opener__badge-prefix\">";
                    // line 143
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Date"));
                    yield "</span>
          <span class=\"bsi-badge bsi-opener__badge bsi-badge--date\">";
                    // line 144
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "group_tagline", [], "any", false, false, true, 144), "created", [], "any", false, false, true, 144), 0, [], "any", false, false, true, 144), "html", null, true);
                    yield "</span>
        </li>
      ";
                }
                // line 147
                yield "    </ul>
  ";
            }
            // line 149
            yield "
";
        } else {
            // line 151
            yield "
  <div class=\"bsi-grid\">
    <h1 class=\"bsi-heading bsi-heading--1\">
      ";
            // line 154
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["content"] ?? null), "title", [], "any", false, false, true, 154), 0, [], "any", false, false, true, 154), "html", null, true);
            yield "
    </h1>
    ";
            // line 156
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\Core\Template\TwigExtension']->withoutFilter(($context["content"] ?? null), "title", "field_media", "body"), "html", null, true);
            yield "
  </div>

";
        }
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["elements", "view_mode", "node", "content", "attributes", "title_prefix", "title_suffix", "name"]);        return; yield;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "themes/custom/bsi_bund/templates/content/node/node--opener--static.html.twig";
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
        return array (  346 => 156,  341 => 154,  336 => 151,  332 => 149,  328 => 147,  322 => 144,  318 => 143,  315 => 142,  312 => 141,  309 => 139,  303 => 136,  299 => 135,  296 => 134,  293 => 133,  290 => 131,  284 => 128,  280 => 127,  273 => 123,  269 => 122,  266 => 121,  263 => 120,  260 => 118,  254 => 115,  250 => 114,  247 => 113,  244 => 112,  241 => 110,  238 => 109,  234 => 106,  228 => 102,  209 => 98,  205 => 97,  201 => 95,  199 => 92,  198 => 91,  194 => 89,  177 => 88,  172 => 85,  169 => 84,  163 => 79,  157 => 77,  151 => 75,  148 => 74,  146 => 73,  140 => 70,  137 => 69,  135 => 68,  132 => 67,  126 => 64,  123 => 63,  121 => 62,  118 => 61,  112 => 58,  109 => 57,  107 => 56,  97 => 49,  89 => 45,  85 => 39,  81 => 38,  78 => 37,  76 => 36,  73 => 35,  67 => 33,  65 => 32,  62 => 31,  60 => 21,  57 => 19,  55 => 16,  54 => 15,  53 => 13,  52 => 12,  51 => 11,  50 => 10,  49 => 7,  47 => 5,  45 => 4,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "themes/custom/bsi_bund/templates/content/node/node--opener--static.html.twig", "/var/www/html/web/themes/custom/bsi_bund/templates/content/node/node--opener--static.html.twig");
    }
    
    public function ensureSecurityChecked(): void
    {
        if ($this->sandbox->isSandboxed($this->source)) {
            $this->checkSecurity();
        }
    }
    
    public function checkSecurity()
    {
        static $tags = ["set" => 4, "if" => 32, "for" => 88, "include" => 91];
        static $filters = ["clean_class" => 11, "escape" => 33, "without" => 77, "t" => 114];
        static $functions = ["attach_library" => 33];
        static $tests = [];

        try {
            $this->sandbox->checkSecurity(
                [0 => "set", 1 => "if", 2 => "for", 3 => "include"],
                [0 => "clean_class", 1 => "escape", 2 => "without", 3 => "t"],
                [0 => "attach_library"],
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
