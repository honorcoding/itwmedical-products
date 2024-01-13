<div class="itwmp-import-export">

    <h1>ITW Medical Products - Import / Export</h1>

    <?php 
        // show messages 
        if ( ! empty( $args['messages'] ) ) {
            foreach( $args['messages'] as $message ) {

                if ( 
                    isset( $message['type'] ) && 
                    isset( $message['text'] )
                ) {            
                    ?>
                    <div class="itwmp-message <?php echo $message['type']; ?>">
                        <?php echo $message['text']; ?>
                    </div>
                    <?php
                }

            }
        }
    ?>

    <div class="section">
        <h2>Import</h2>
        <?php echo $args['import_section'] ?>
    </div>

    <div class="section">
        <h2>Export</h2>
        <?php echo $args['export_section'] ?>
    </div>

</div> 