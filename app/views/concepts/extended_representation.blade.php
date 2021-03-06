    <div class="concept">
      <div class="heading">
        {{ $concept->representation() }}
      </div>
      @if ($concept->getType() == 'Geographic')
        <div>Type: sted / geografisk navn</div>
      @elseif ($concept->getType() == 'Temporal')
        <div>Type: tidspunkt / tidsperiode</div>
      @elseif ($concept->getType() == 'GenreForm')
        <div>Type: sjanger / form</div>
      @endif

      <ul class="flat">
      @foreach ($concept->labels as $label)
        @if ($label->lang == 'nb' && $label->class == 'altLabel')
          <li class="icon">{{ $label->value }}</li>
        @elseif ($label->lang != 'nb' && $label->class == 'prefLabel')
          <li class="icon">{{ $label->value }} <em>({{ $label->lang }})</em></li>
        @endif
      @endforeach
      </ul>

      @if (count($related) || count($notes))
        <h4>Notes and references</h4>
        <ul>
        @foreach ($related as $rel)
          <li class="icon">
            <em class="glyphicon glyphicon-hand-right"></em>
            Se også <a href="/concepts/{{ $concept->vocabulary->label }}/{{ $rel[0] }}">{{ $rel[1] }}</a>
          </li>
        @endforeach
        @foreach ($notes as $note)
          <li class="icon">
            <em class="glyphicon glyphicon-chevron-right"></em>
            {{ $note}}
          </li>
        @endforeach
        </ul>
      @endif

      @if ($tree != '')
        <h4>Broader</h4>
        {{ $tree }}
      @endif

      <h4>{{ Lang::get('relationships.other_relationships') }}</h4>
      <ul>
      @if (!count($otherRelationships))
        <li><em>None yet</em></li>
      @else
        @foreach ($otherRelationships as $rel)
          <li class="icon">
            {{ $rel->representationFrom($concept) }}
          </li>
        @endforeach
      @endif
        <li><a href="/relationships/create?{{ $role }}={{ $concept->vocabulary->id }}:{{ $concept->identifier }}">+ New mapping</a></li>
      </ul>

      <h4>External sources</h4>
      {{ $concept->getRelatedContent() }}
    </div>
