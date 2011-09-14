KalturaThumbRotator = {

        slices : 16, // number of thumbs per video
        frameRate : 1000, // frameRate in milliseconds for changing the thumbs
       
        timer : null,
        slice : 0,
        img  : new Image(),
       
        thumbBase : function (o) // extract the base thumb path by removing the slicing parameters
        {
                var path = o.src;
                var pos = path.indexOf("/vid_slice");
                if (pos != -1)
                        path = path.substring(0, pos);
                       
                return path;
        },
       

        change : function (o, i) // set the Nth thumb, request the next one and set a timer for showing it
        {
                slice = (i + 1) % this.slices;

                var path = this.thumbBase(o);
               
                o.src = path + "/vid_slice/" + i + "/vid_slices/" + this.slices;
                this.img.src = path + "/vid_slice/" + slice + "/vid_slices/" + this.slices;

                i = i % this.slices;
                i++;
               
                this.timer = setTimeout(function () { KalturaThumbRotator.change(o, i) }, this.frameRate);
        },
       
        start : function (o) // reset the timer and show the first thumb
        {
                clearTimeout(this.timer);
                var path = this.thumbBase(o);
                this.change(o, 1);
        },

        end : function (o) // reset the timer and restore the base thumb
        {
                clearTimeout(this.timer);
                o.src = this.thumbBase(o);
        }
};


