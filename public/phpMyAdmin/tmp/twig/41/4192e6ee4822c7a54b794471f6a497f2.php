<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* database/triggers/row.twig */
class __TwigTemplate_109e275aeeb5f6255f5331c7c78d0529 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        yield "<tr";
        if ( !Twig\Extension\CoreExtension::testEmpty(($context["row_class"] ?? null))) {
            yield " class=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["row_class"] ?? null), "html", null, true);
            yield "\"";
        }
        yield ">
  <td>
    <input type=\"checkbox\" class=\"checkall\" name=\"item_name[]\" value=\"";
        // line 3
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["trigger"] ?? null), "name", [], "any", false, false, false, 3), "html", null, true);
        yield "\">
  </td>
  <td>
    <span class='drop_sql hide'>";
        // line 6
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["trigger"] ?? null), "drop", [], "any", false, false, false, 6), "html", null, true);
        yield "</span>
    <strong>";
        // line 7
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["trigger"] ?? null), "name", [], "any", false, false, false, 7), "html", null, true);
        yield "</strong>
  </td>
  ";
        // line 9
        if (Twig\Extension\CoreExtension::testEmpty(($context["table"] ?? null))) {
            // line 10
            yield "    <td>
      <a href=\"";
            // line 11
            yield PhpMyAdmin\Url::getFromRoute("/table/triggers", ["db" => ($context["db"] ?? null), "table" => CoreExtension::getAttribute($this->env, $this->source, ($context["trigger"] ?? null), "table", [], "any", false, false, false, 11)]);
            yield "\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["trigger"] ?? null), "table", [], "any", false, false, false, 11), "html", null, true);
            yield "</a>
    </td>
  ";
        }
        // line 14
        yield "  <td>
    ";
        // line 15
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["trigger"] ?? null), "action_timing", [], "any", false, false, false, 15), "html", null, true);
        yield "
  </td>
  <td>
    ";
        // line 18
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["trigger"] ?? null), "event_manipulation", [], "any", false, false, false, 18), "html", null, true);
        yield "
  </td>
  <td>
    ";
        // line 21
        if (($context["has_edit_privilege"] ?? null)) {
            // line 22
            yield "      <a class=\"ajax edit_anchor\" href=\"";
            yield PhpMyAdmin\Url::getFromRoute("/database/triggers", ["db" =>             // line 23
($context["db"] ?? null), "table" =>             // line 24
($context["table"] ?? null), "edit_item" => true, "item_name" => CoreExtension::getAttribute($this->env, $this->source,             // line 26
($context["trigger"] ?? null), "name", [], "any", false, false, false, 26)]);
            // line 27
            yield "\">
        ";
            // line 28
            yield PhpMyAdmin\Html\Generator::getIcon("b_edit", _gettext("Edit"));
            yield "
      </a>
    ";
        } else {
            // line 31
            yield "      ";
            yield PhpMyAdmin\Html\Generator::getIcon("bd_edit", _gettext("Edit"));
            yield "
    ";
        }
        // line 33
        yield "  </td>
  <td>
    <a class=\"ajax export_anchor\" href=\"";
        // line 35
        yield PhpMyAdmin\Url::getFromRoute("/database/triggers", ["db" =>         // line 36
($context["db"] ?? null), "table" =>         // line 37
($context["table"] ?? null), "export_item" => true, "item_name" => CoreExtension::getAttribute($this->env, $this->source,         // line 39
($context["trigger"] ?? null), "name", [], "any", false, false, false, 39)]);
        // line 40
        yield "\">
      ";
        // line 41
        yield PhpMyAdmin\Html\Generator::getIcon("b_export", _gettext("Export"));
        yield "
    </a>
  </td>
  <td>
    ";
        // line 45
        if (($context["has_drop_privilege"] ?? null)) {
            // line 46
            yield "      ";
            yield PhpMyAdmin\Html\Generator::linkOrButton(PhpMyAdmin\Url::getFromRoute("/sql"), ["db" =>             // line 49
($context["db"] ?? null), "table" =>             // line 50
($context["table"] ?? null), "sql_query" => CoreExtension::getAttribute($this->env, $this->source,             // line 51
($context["trigger"] ?? null), "drop", [], "any", false, false, false, 51), "goto" => PhpMyAdmin\Url::getFromRoute("/database/triggers", ["db" =>             // line 52
($context["db"] ?? null)])], PhpMyAdmin\Html\Generator::getIcon("b_drop", _gettext("Drop")), ["class" => "ajax drop_anchor"]);
            // line 56
            yield "
    ";
        } else {
            // line 58
            yield "      ";
            yield PhpMyAdmin\Html\Generator::getIcon("bd_drop", _gettext("Drop"));
            yield "
    ";
        }
        // line 60
        yield "  </td>
</tr>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "database/triggers/row.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable()
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  152 => 60,  146 => 58,  142 => 56,  140 => 52,  139 => 51,  138 => 50,  137 => 49,  135 => 46,  133 => 45,  126 => 41,  123 => 40,  121 => 39,  120 => 37,  119 => 36,  118 => 35,  114 => 33,  108 => 31,  102 => 28,  99 => 27,  97 => 26,  96 => 24,  95 => 23,  93 => 22,  91 => 21,  85 => 18,  79 => 15,  76 => 14,  68 => 11,  65 => 10,  63 => 9,  58 => 7,  54 => 6,  48 => 3,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "database/triggers/row.twig", "/Users/benjamin/tictoque/public/phpMyAdmin/templates/database/triggers/row.twig");
    }
}
