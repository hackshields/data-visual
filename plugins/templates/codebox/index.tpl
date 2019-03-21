<pre><code class="{$language}">{$code}</code></pre>
<script>
$(function() {
    $('pre code').each(function(i, block) {
    hljs.highlightBlock(block);
    });
  })
</script>

