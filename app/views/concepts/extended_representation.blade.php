    <div class="concept">
      <div class="heading">
        {{ $concept->representation() }}
      </div>

      <ul class="flat">
      @foreach ($concept->labels as $label)
        @if ($label->lang == 'nb' && $label->class == 'altLabel')
          <li>{{ $label->value }}</li>
        @endif
      @endforeach
      </ul>

      @if (count($notes))
        <h4>Notes</h4>
        <ul>
        @foreach ($notes as $note)
          <li style="margin: 0 0 8px 0;">
            <em class="glyphicon glyphicon-hand-right"></em>
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
          <li>
            <em class="glyphicon glyphicon-link" style="color:gray"></em>
            {{ $rel->representationFrom($concept) }}
          </li>
        @endforeach
      @endif
      </ul>

      <h4>Reference</h4>
      {{ $concept->getRelatedContent() }}
    </div>
