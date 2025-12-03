#!/bin/bash
set -e

# =======================================
# 🧪 Codeception Multi-suite Coverage Runner
# =======================================

# 套件名称（首字母大写）
suites=("Unit" "Integration" "Acceptance")

# 报告路径
base_dir="$(pwd)/tests/_output"
output_dir="$base_dir/_report"
min_coverage=75

# 清理旧报告
echo "🧹 Cleaning old reports..."
rm -rf "$output_dir"
mkdir -p "$output_dir"

echo "🧪 Running Codeception test suites..."
echo

for suite in "${suites[@]}"; do
    echo "=========================================="
    echo "▶️  Running $suite tests..."
    echo "=========================================="

    if [ "$suite" == "Acceptance" ]; then
        vendor/bin/codecept run "$suite"
        echo "ℹ️  Skipped coverage for $suite (E2E tests)"
        continue
    fi

    html_dir="$output_dir/$suite"
    xml_file="$output_dir/coverage-$suite.xml"

    vendor/bin/codecept run "$suite" \
        --coverage-xml "$xml_file" \
        --coverage-html "$html_dir"

    if [ ! -f "$xml_file" ]; then
        echo "❌ Coverage XML not found for $suite!"
        exit 1
    fi

    echo "✅ Coverage XML saved: $xml_file"
    echo "✅ HTML report saved:  $html_dir"
    echo
done

# =======================================
# 📊 合并覆盖率报告
# =======================================
echo "📦 Merging coverage reports..."

combined_xml="$output_dir/coverage-total.xml"
php -r '
$dir = "'"$output_dir"'";
$xmlFiles = glob("$dir/coverage-*.xml");
if (empty($xmlFiles)) { exit(0); }

$dom = new DOMDocument();
$dom->load($xmlFiles[0]);
$project = $dom->getElementsByTagName("project")->item(0);

for ($i = 1; $i < count($xmlFiles); $i++) {
    $xml = new DOMDocument();
    $xml->load($xmlFiles[$i]);
    foreach ($xml->getElementsByTagName("file") as $file) {
        $import = $dom->importNode($file, true);
        $project->appendChild($import);
    }
}

// 保存到由 Bash 展开的路径字面量
$combinedPath = "'"$combined_xml"'";
$dom->save($combinedPath);

// 用 PHP 打印时直接使用 $combinedPath（已定义）
echo "✅ Combined XML coverage saved to " . $combinedPath . PHP_EOL;
'

# =======================================
# 🧮 计算总覆盖率
# =======================================
coverage_percent=$(php -r '
$combined = "'"$combined_xml"'";
if (!file_exists($combined)) { echo 0; exit(0); }
$xml = simplexml_load_file($combined);
$total = 0; $covered = 0;
foreach ($xml->project->package as $pkg) {
    foreach ($pkg->file as $f) {
        $m = $f->metrics;
        $total += (int)$m["statements"];
        $covered += (int)$m["coveredstatements"];
    }
}
if ($total == 0) { echo 0; exit(0); }
echo round(($covered / $total) * 100);
')

echo "📊 Total Coverage: ${coverage_percent}%"

if [ "$coverage_percent" -lt "$min_coverage" ]; then
    echo "⚠️  Coverage below target (${min_coverage}%)"
else
    echo "✅ Coverage meets target (${coverage_percent}%)"
fi

# =======================================
# 🌐 生成总览 HTML 报告
# =======================================
# ---- 生成总览 HTML（用 Bash heredoc，避免 php -r 的引号问题） ----
summary_html="$output_dir/index.html"

# 计算 Unit / Integration 单项覆盖率（你脚本已有类似代码；这里假定 coverage-Unit.xml/exist）
unit_coverage=0
integration_coverage=0
if [ -f "$output_dir/coverage-Unit.xml" ]; then
  unit_coverage=$(php -r '
$xml = simplexml_load_file("'"$output_dir"'/coverage-Unit.xml");
$total=0;$covered=0;
foreach($xml->project->package as $pkg){ foreach($pkg->file as $f){ $m=$f->metrics; if($m){ $total+=(int)$m["statements"]; $covered+=(int)$m["coveredstatements"]; } } }
echo ($total===0?0:round(($covered/$total)*100));
')
fi

if [ -f "$output_dir/coverage-Integration.xml" ]; then
  integration_coverage=$(php -r '
$xml = simplexml_load_file("'"$output_dir"'/coverage-Integration.xml");
$total=0;$covered=0;
foreach($xml->project->package as $pkg){ foreach($pkg->file as $f){ $m=$f->metrics; if($m){ $total+=(int)$m["statements"]; $covered+=(int)$m["coveredstatements"]; } } }
echo ($total===0?0:round(($covered/$total)*100));
')
fi

# 状态样式（good / bad）
if [ -n "$coverage_percent" ] && [ "$coverage_percent" -ge "$min_coverage" ]; then
  total_class="good"
else
  total_class="bad"
fi

# write html (Bash heredoc expands variables)
cat > "$summary_html" <<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Coverage Summary</title>
<style>
body{font-family:Arial, sans-serif;margin:40px;}
h1{color:#333;}
.section{margin-top:30px;}
.coverage-bar { background: #eee; border-radius: 5px; overflow: hidden; height: 24px; margin: 8px 0; width: 60%; }
.coverage-fill { height: 100%; line-height: 24px; color: white; text-align: center; font-weight: bold; }
.coverage-total { background-color: #4CAF50; }
.coverage-unit { background-color: #2196F3; }
.coverage-integration { background-color: #FF9800; }
.coverage-acceptance { background-color: #9E9E9E; }
.good{color:green;font-weight:bold;}
.bad{color:red;font-weight:bold;}
a { color: #0366d6; }
</style>
</head>
<body>
<h1>📊 Code Coverage Summary</h1>

<p><b>Total Coverage:</b> <span class="${total_class}">${coverage_percent}%</span> <small>(Minimum required: ${min_coverage}%)</small></p>

<h2>Total Coverage</h2>
<div class="coverage-bar">
  <div class="coverage-fill coverage-total" style="width: ${coverage_percent}%;"><b>${coverage_percent}%</b></div>
</div>

<h2>Unit Coverage: ${unit_coverage}%</h2>
<div class="coverage-bar">
  <div class="coverage-fill coverage-unit" style="width: ${unit_coverage}%;"><b>${unit_coverage}%</b></div>
</div>

<h2>Integration Coverage: ${integration_coverage}%</h2>
<div class="coverage-bar">
  <div class="coverage-fill coverage-integration" style="width: ${integration_coverage}%;"><b>${integration_coverage}%</b></div>
</div>

<h2>Acceptance Coverage: 0% (未收集)</h2>
<div class="coverage-bar">
  <div class="coverage-fill coverage-acceptance" style="width: 0%;"><b>0% (未收集)</b></div>
</div>

<div class="section">
  <h2>Detailed Reports</h2>
  <ul>
    <li><a href="Unit/index.html">Unit Tests (HTML report)</a></li>
    <li><a href="Integration/index.html">Integration Tests (HTML report)</a></li>
  </ul>
</div>

</body>
</html>
HTML

echo "✅ Summary HTML generated at $summary_html"


echo "=========================================="
echo "🎉 All done! Reports available at:"
echo "  - Combined XML: $combined_xml"
echo "  - Combined HTML Summary: $summary_html"
echo "  - Individual Reports:"
echo "      Unit: $output_dir/Unit"
echo "      Integration: $output_dir/Integration"
echo "=========================================="
