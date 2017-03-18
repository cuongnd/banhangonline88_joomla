package vantinviet.banhangonline88.entities.messenger;

import com.google.gson.annotations.SerializedName;

import java.util.List;

import vantinviet.banhangonline88.entities.Metadata;

public class MessengerListResponse {

    private Metadata metadata;

    @SerializedName("records")
    private List<Messenger> Messengers;

    public Metadata getMetadata() {
        return metadata;
    }

    public void setMetadata(Metadata metadata) {
        this.metadata = metadata;
    }

    public List<Messenger> getMessengers() {
        return Messengers;
    }

    public void setMessengers(List<Messenger> Messengers) {
        this.Messengers = Messengers;
    }

    @Override
    public boolean equals(Object o) {
        if (this == o) return true;
        if (!(o instanceof MessengerListResponse)) return false;

        MessengerListResponse that = (MessengerListResponse) o;

        if (getMetadata() != null ? !getMetadata().equals(that.getMetadata()) : that.getMetadata() != null) return false;
        return !(getMessengers() != null ? !getMessengers().equals(that.getMessengers()) : that.getMessengers() != null);

    }

    @Override
    public int hashCode() {
        int result = getMetadata() != null ? getMetadata().hashCode() : 0;
        result = 31 * result + (getMessengers() != null ? getMessengers().hashCode() : 0);
        return result;
    }

    @Override
    public String toString() {
        return "MessengerListResponse{" +
                "metadata=" + metadata +
                ", Messengers=" + Messengers +
                '}';
    }
}
