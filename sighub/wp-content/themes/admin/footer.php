<?php
if(!isset($_GET['beIframe'])){
if(!is_404()&&!is_page()) : ?>
	<!--?php get_sidebar(); ?-->
<?php endif; ?>
</article>
</div>
</div>
</div>
<?php wp_footer(); ?>
</body>
</html>
<?php }
    else{
?>
                 </div><!-- #main wrap -->
             </div><!-- #main .wrapper -->
        </div><!-- #page -->
        </body>
        </html>
<?php }?>