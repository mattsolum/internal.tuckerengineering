<a href="<?PHP echo(site_url($result->link)); ?>"><?PHP echo($result->title); ?></a>
<p><?PHP echo($result->highlight_search_terms($result->excerpt($keywords), $keywords)); ?></p>
<a href="<?PHP echo(site_url($result->link));  ?>"><?PHP echo(site_url($result->link)); ?></a>