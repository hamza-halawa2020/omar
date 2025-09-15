<div class="modal fade" id="transactionModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="receiveForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title fw-bold fs-5">Receive Transaction</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">


                    <div class="d-flex gap-3">
                        <div>
                            <label>Amount</label>
                            <input type="number" name="amount" class="form-control" required>
                        </div>
                        <div>
                            <label>Commission</label>
                            <input type="number" name="commission" class="form-control" required>
                        </div>
                    </div>
                    <label>Attachment</label>
                    <input type="file" name="attachment" class="form-control">
                    <label>Notes</label>
                    <textarea name="notes" class="form-control"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-sm">Save</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>
