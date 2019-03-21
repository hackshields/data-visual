<div class="row row-cards-pf">
  <div class="card-pf">
    <div class="card-pf-heading">
      <h2 class="card-pf-title">{$apptitle|default:'&nbsp;'}</h2>
    </div>
    <div class="card-pf-body">
      {section name=i loop=$data}
      <div class="progress-description">{$data[i].label}</div>
      <div class="progress progress-label-top-right">
        <div class="progress-bar" role="progressbar" aria-valuenow="{$data[i].current}" aria-valuemin="0" aria-valuemax="{$data[i].total}" style="width:{$data[i].current*100/$data[i].total}%;"  data-toggle="tooltip" title="{$data[i].current*100/$data[i].total}%">
          <span><strong>{$data[i].current} of {$data[i].total}</strong></span>
        </div>
        <div class="progress-bar progress-bar-remaining" role="progressbar" aria-valuenow="{$data[i].total - $data[i].current}" aria-valuemin="0" aria-valuemax="{$data[i].total}" style="width: {($data[i].total - $data[i].current)*100/$data[i].total}%;" data-toggle="tooltip">
          <span class="sr-only"></span>
        </div>
      </div>
      {/section}
    </div>
  </div>
</div>
