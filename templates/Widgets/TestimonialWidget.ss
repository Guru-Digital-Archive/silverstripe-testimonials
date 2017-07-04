<% if $Testimonials %>
<ul class="testimonials cycle-slideshow" data-cycle-fx="fadeout" data-cycle-auto-height="4:3" data-cycle-delay="-1500">
    <% loop  $Testimonials  %>
    <li class="testimonial-item $FirstLast">
        $Me
    </li>
    <% end_loop %>
</ul>
<% end_if %>

<% if $MoreLink %>
<a class="btn btn-default more-testimonials"  href="$MoreLink.Link" title="$MoreLink.Title.XML">$BtnText</a>
<% end_if %>