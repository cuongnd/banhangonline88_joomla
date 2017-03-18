package vantinviet.banhangonline88.entities.messenger;


import com.google.gson.annotations.SerializedName;

import java.util.List;

public class Messenger {

    private long id;

    private int from;
    private String fromuserid;
    private String fromuser;
    private String userid;
    private String profilelink;
    private String time;
    private String message;
    private String self;
    private String old;

    @SerializedName("main_image")
    private String avatar;

    @SerializedName("main_image_high_res")
    private String mainImageHighRes;
    private List<MessengerVariant> variants;
    private List<Messenger> related;

    public Messenger() {
    }

    public long getId() {
        return id;
    }

    public void setId(long id) {
        this.id = id;
    }

    public String getMessage() {
        return message;
    }

    public void setMessage(String message) {
        this.message = message;
    }

    public String getAvatar() {
        return avatar;
    }

    public void setAvatar(String avatar) {
        this.avatar = avatar;
    }

    public String getMainImageHighRes() {
        return mainImageHighRes;
    }

    public void setMainImageHighRes(String mainImageHighRes) {
        this.mainImageHighRes = mainImageHighRes;
    }

    public List<MessengerVariant> getVariants() {
        return variants;
    }

    public void setVariants(List<MessengerVariant> variants) {
        this.variants = variants;
    }

    public List<Messenger> getRelated() {
        return related;
    }

    public void setRelated(List<Messenger> related) {
        this.related = related;
    }

    @Override
    public boolean equals(Object o) {
        if (this == o) return true;
        if (o == null || getClass() != o.getClass()) return false;

        Messenger product = (Messenger) o;

        if (id != product.id) return false;
        if (message != null ? !message.equals(product.message) : product.message != null) return false;
        if (avatar != null ? !avatar.equals(product.avatar) : product.avatar != null)
            return false;
        if (mainImageHighRes != null ? !mainImageHighRes.equals(product.mainImageHighRes) : product.mainImageHighRes != null)
            return false;
        if (variants != null ? !variants.equals(product.variants) : product.variants != null)
            return false;
        return !(related != null ? !related.equals(product.related) : product.related != null);

    }

    @Override
    public int hashCode() {
        int result;
        long temp;
        result = (int) (id ^ (id >>> 32));
        result = 31 * result + (message != null ? message.hashCode() : 0);
        result = 31 * result + (avatar != null ? avatar.hashCode() : 0);
        result = 31 * result + (mainImageHighRes != null ? mainImageHighRes.hashCode() : 0);
        result = 31 * result + (variants != null ? variants.hashCode() : 0);
        result = 31 * result + (related != null ? related.hashCode() : 0);
        return result;
    }

    @Override
    public String toString() {
        return "Messenger{" +
                "id=" + id +
                ", message='" + message + '\'' +
                ", avatar='" + avatar + '\'' +
                ", mainImageHighRes='" + mainImageHighRes + '\'' +
                ", variants=" + variants +
                ", related=" + related +
                '}';
    }
}

