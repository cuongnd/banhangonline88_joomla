package vantinviet.banhangonline88.entities.messenger;


public class MessengerQuantity {

    private int quantity;
    private String value;

    public MessengerQuantity() {
    }

    public MessengerQuantity(int quantity, String value) {
        this.quantity = quantity;
        this.value = value;
    }

    public int getQuantity() {
        return quantity;
    }

    public void setQuantity(int quantity) {
        this.quantity = quantity;
    }

    public String getValue() {
        return value;
    }

    public void setValue(String value) {
        this.value = value;
    }

    @Override
    public boolean equals(Object o) {
        if (this == o) return true;
        if (o == null || getClass() != o.getClass()) return false;

        MessengerQuantity that = (MessengerQuantity) o;

        if (quantity != that.quantity) return false;
        return !(value != null ? !value.equals(that.value) : that.value != null);

    }

    @Override
    public int hashCode() {
        int result = quantity;
        result = 31 * result + (value != null ? value.hashCode() : 0);
        return result;
    }

    @Override
    public String toString() {
        return value;
    }
}
