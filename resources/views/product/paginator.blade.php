@if ($paginator->lastPage() > 1)
<ul class="pagination">
    @for ($i = 1; $i <= $paginator->lastPage(); $i++)
        <li class="{{ ($paginator->currentPage() == $i) ? ' active' : '' }}">
        <a href="javascript:;" onclick="getReview('{{ $productId }}', '{{ $i }}')">{{ $i }}</a>
        </li>
    @endfor
</ul>
@endif